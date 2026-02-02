# EOI Compose Modal - Troubleshooting Guide

## 🔴 Common Error: "Error loading documents. Please try again."

### Quick Fix Steps

#### 1. **Clear Browser Cache & Hard Refresh**
```
Press: Ctrl + Shift + R (Windows/Linux)
Or: Cmd + Shift + R (Mac)
```

#### 2. **Clear Laravel Caches**
```bash
cd c:\xampp\htdocs\migrationmanager2
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

#### 3. **Check Console for Detailed Errors**
1. Open browser DevTools (F12)
2. Go to Console tab
3. Click "Send to Client" button
4. Look for logs starting with `[EOI-COMPOSE]`

**Expected logs:**
```
[EOI-COMPOSE] Opening modal with: {eoiId: 123, eoiNumber: "E012345", clientId: 456}
[EOI-COMPOSE] Loading email preview for client: 456 eoi: 123
[EOI-COMPOSE] Preview URL: /clients/456/eoi-roi/123/email-preview
[EOI-COMPOSE] Loading visa documents for client: 456 eoi: E012345
[EOI-COMPOSE] Documents URL: /clients/456/eoi-roi/visa-documents
```

---

## 🔍 Diagnostic Steps

### Step 1: Verify Routes Are Registered
```bash
php artisan route:list --name=eoi-roi
```

**Expected output should include:**
```
GET|HEAD   clients/{client}/eoi-roi/visa-documents
GET|HEAD   clients/{client}/eoi-roi/{eoiReference}/email-preview  
POST       clients/{client}/eoi-roi/{eoiReference}/send-email
```

### Step 2: Test Endpoints Directly

**Option A: Use Test Page**
1. Navigate to: `http://localhost/test-eoi-compose.html`
2. Enter Client ID (database ID, e.g., 123)
3. Enter EOI ID (e.g., 456)
4. Enter EOI Number (e.g., E012345)
5. Click "Test Email Preview" and "Test Visa Documents"

**Option B: Use Browser Console**
```javascript
// Test email preview
$.get('/clients/123/eoi-roi/456/email-preview')
    .done(data => console.log('SUCCESS:', data))
    .fail(xhr => console.log('ERROR:', xhr.status, xhr.responseText));

// Test visa documents
$.get('/clients/123/eoi-roi/visa-documents?eoi_number=E012345')
    .done(data => console.log('SUCCESS:', data))
    .fail(xhr => console.log('ERROR:', xhr.status, xhr.responseText));
```

**Option C: Use Postman or curl**
```bash
# Test email preview
curl -X GET "http://localhost/clients/123/eoi-roi/456/email-preview" \
  -H "Accept: application/json" \
  -b "your_session_cookie"

# Test visa documents  
curl -X GET "http://localhost/clients/123/eoi-roi/visa-documents?eoi_number=E012345" \
  -H "Accept: application/json" \
  -b "your_session_cookie"
```

### Step 3: Check Laravel Logs
```bash
# View latest errors
tail -n 50 storage/logs/laravel.log

# Or open in editor
notepad storage/logs/laravel.log
```

Look for errors containing:
- `ClientEoiRoiController`
- `getVisaDocuments`
- `getEmailPreview`

---

## 🐛 Common Issues & Solutions

### Issue 1: "404 Not Found" Errors

**Symptoms:**
- Console shows 404 errors
- Endpoints return "Not Found"

**Causes:**
- Routes not registered
- Wrong route parameters

**Solutions:**
1. Clear route cache: `php artisan route:clear`
2. Verify routes exist: `php artisan route:list --name=eoi-roi`
3. Check Laravel logs for route binding issues

---

### Issue 2: "401 Unauthorized" or "403 Forbidden"

**Symptoms:**
- Console shows 401/403 errors
- "Unauthorized" or "Forbidden" response

**Causes:**
- Not logged in as admin
- Session expired
- CSRF token mismatch

**Solutions:**
1. Refresh page and log in again
2. Check session in `storage/framework/sessions`
3. Verify CSRF token exists:
```javascript
// In browser console
$('meta[name="csrf-token"]').attr('content')
```

---

### Issue 3: "500 Internal Server Error"

**Symptoms:**
- Console shows 500 errors
- "Something went wrong" messages

**Causes:**
- PHP/Laravel errors in controller
- Database connection issues
- Missing dependencies

**Solutions:**
1. Check Laravel logs: `tail -n 50 storage/logs/laravel.log`
2. Enable debug mode in `.env`:
```
APP_DEBUG=true
```
3. Check for specific errors:
   - Model not found (EOI or Client)
   - S3 storage issues
   - Email template rendering errors

**Common error patterns:**

**Error: "Model [App\Models\Admin] not found"**
```
Solution: Client ID is invalid or client doesn't exist
Check: SELECT * FROM admins WHERE id = 123;
```

**Error: "View [emails.eoi_confirmation] not found"**
```
Solution: Email template missing
Check: File exists at resources/views/emails/eoi_confirmation.blade.php
```

**Error: "S3 credentials not configured"**
```
Solution: Check .env file:
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=your_region
AWS_BUCKET=your_bucket
```

---

### Issue 4: "timeout" or "nettimeout" Errors

**Symptoms:**
- Requests hang and timeout
- Console shows "nettimeout FAILED"

**Causes:**
- Database query too slow
- S3 requests timing out
- Large number of documents

**Solutions:**
1. Increase AJAX timeout (already set to 30s in updated code)
2. Optimize database queries:
```php
// Check for N+1 queries
// Add eager loading if needed
```
3. Check S3 connectivity:
```bash
php artisan tinker
>>> Storage::disk('s3')->exists('test.txt');
```

---

### Issue 5: Client ID Not Set Correctly

**Symptoms:**
- Console shows `clientId: null` or `clientId: undefined`
- URLs like `/clients/null/eoi-roi/...`

**Causes:**
- `ClientDetailConfig` not defined
- Using wrong property name

**Solutions:**
1. Check console for `ClientDetailConfig`:
```javascript
console.log(window.ClientDetailConfig);
```

**Expected output:**
```javascript
{
  clientId: 123,
  encodeId: "abc123encoded",
  matterId: 456,
  // ... other properties
}
```

2. If `ClientDetailConfig` is missing:
   - Ensure you're on client detail page
   - Check `resources/views/crm/clients/detail.blade.php` line ~1305

3. If `clientId` is wrong type:
   - Should be numeric (e.g., `123`)
   - NOT string (e.g., `"abc123"`)
   - NOT encoded (e.g., `"xyz789encoded"`)

---

### Issue 6: EOI Record Not Found

**Symptoms:**
- "EOI record not found" error
- 404 on email-preview endpoint

**Causes:**
- EOI not saved yet
- Wrong EOI ID
- EOI belongs to different client

**Solutions:**
1. Verify EOI exists:
```sql
SELECT * FROM client_eoi_references WHERE id = 456;
```

2. Verify EOI belongs to client:
```sql
SELECT * FROM client_eoi_references 
WHERE id = 456 AND client_id = 123;
```

3. Ensure EOI is saved before clicking "Send to Client"

---

### Issue 7: No Documents Returned

**Symptoms:**
- Modal opens successfully
- Shows "No visa documents available"
- But documents exist

**Causes:**
- Documents not of type "visa"
- Documents marked as `not_used_doc`
- Documents missing file

**Solutions:**
1. Check documents exist:
```sql
SELECT id, file_name, doc_type, type, not_used_doc, myfile 
FROM documents 
WHERE client_id = 123 AND doc_type = 'visa';
```

2. Check document filters:
   - `doc_type` must be `'visa'`
   - `type` must be `'client'`
   - `not_used_doc` must be `NULL`
   - `myfile` must be `NOT NULL`

3. If documents exist but not showing:
   - Check S3 file exists
   - Verify `folder_name` references valid `VisaDocumentType`

---

## 🔧 Advanced Debugging

### Enable Query Logging
```php
// In ClientEoiRoiController.php, add at top of getVisaDocuments():
\DB::enableQueryLog();

// At end of method, before return:
\Log::info('Visa Documents Query Log', \DB::getQueryLog());
```

### Test Backend Methods Directly
```bash
php artisan tinker

# Test getVisaDocuments
$client = \App\Models\Admin::find(123);
$controller = new \App\Http\Controllers\CRM\ClientEoiRoiController(app(\App\Services\PointsService::class));
$request = new \Illuminate\Http\Request(['eoi_number' => 'E012345']);
$response = $controller->getVisaDocuments($request, $client);
print_r($response->getData());

# Test getEmailPreview
$eoi = \App\Models\ClientEoiReference::find(456);
$response = $controller->getEmailPreview($client, $eoi);
print_r($response->getData());
```

---

## 📊 Success Indicators

When everything is working correctly, you should see:

### ✅ Console Logs (No Errors)
```
[EOI-COMPOSE] Opening modal with: {eoiId: 456, eoiNumber: "E012345", clientId: 123}
[EOI-COMPOSE] Current compose state: {eoiId: 456, eoiNumber: "E012345", clientId: 123}
[EOI-COMPOSE] Loading email preview for client: 123 eoi: 456
[EOI-COMPOSE] Preview URL: /clients/123/eoi-roi/456/email-preview
[EOI-COMPOSE] Preview loaded successfully: {success: true, data: {...}}
[EOI-COMPOSE] Loading visa documents for client: 123 eoi: E012345
[EOI-COMPOSE] Documents URL: /clients/123/eoi-roi/visa-documents
[EOI-COMPOSE] Documents loaded successfully: {success: true, data: {...}}
```

### ✅ Modal Display
- To field shows: "Client Name <email@example.com>"
- Subject field shows: "Please Confirm Your EOI Details - E012345"
- Body textarea shows email content (not "Loading...")
- Attachments section shows:
  - "Documents referencing E012345" (if any)
  - "Other Visa Documents" (if any)
  - OR "No visa documents available" (if none)

### ✅ Network Tab (200 OK)
```
GET /clients/123/eoi-roi/456/email-preview          200 OK
GET /clients/123/eoi-roi/visa-documents?eoi_number=E012345  200 OK
```

---

## 🆘 Still Having Issues?

### Check These Files Are Updated:
- ✅ `app/Http/Controllers/CRM/ClientEoiRoiController.php`
- ✅ `routes/clients.php`
- ✅ `public/js/clients/eoi-roi.js`
- ✅ `resources/views/crm/clients/tabs/eoi_roi.blade.php`

### Verify File Timestamps:
```bash
ls -la app/Http/Controllers/CRM/ClientEoiRoiController.php
ls -la routes/clients.php  
ls -la public/js/clients/eoi-roi.js
ls -la resources/views/crm/clients/tabs/eoi_roi.blade.php
```

All should be dated today (February 2, 2026).

### Last Resort: Full Cache Clear
```bash
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
composer dump-autoload
```

Then hard refresh browser (Ctrl+Shift+R).

---

## 📞 Support Contacts

If issue persists after following all steps:

1. **Capture Error Details:**
   - Full console output (copy/paste)
   - Network tab showing failed requests
   - Laravel log excerpts
   - Screenshots

2. **Provide Context:**
   - Client ID being used
   - EOI ID being used
   - Browser and version
   - PHP version: `php -v`
   - Laravel version: `php artisan --version`

3. **Check Database:**
```sql
-- Verify data exists
SELECT * FROM admins WHERE id = 123;
SELECT * FROM client_eoi_references WHERE id = 456;
SELECT * FROM documents WHERE client_id = 123 AND doc_type = 'visa';
```

---

**Last Updated:** February 2, 2026  
**Version:** 1.0  
**Status:** Active
