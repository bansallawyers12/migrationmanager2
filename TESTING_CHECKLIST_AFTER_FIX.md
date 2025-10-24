# Testing Checklist After 404 Fix

**Date:** October 24, 2025  
**Purpose:** Verify all fixed URLs are working correctly  
**Status:** Ready for Testing

---

## 🧪 Critical Features to Test

### 1. Office Visit Management ⚠️ HIGH PRIORITY
**URLs Fixed:** 28 occurrences

**Test Steps:**
- [ ] Navigate to Office Visits (`/office-visits`)
- [ ] Click "Check In" to create a new office visit
  - [ ] Form should submit to `/checkin` (not `/admin/checkin`)
- [ ] Update visit purpose
  - [ ] Should POST to `/update_visit_purpose`
- [ ] Update visit comment
  - [ ] Should POST to `/update_visit_comment`
- [ ] Mark as "Attending"
  - [ ] Should POST to `/attend_session`
- [ ] Complete session
  - [ ] Should POST to `/complete_session`
- [ ] View checkin details
  - [ ] Should GET `/get-checkin-detail`

**Expected Result:** All office visit operations work without 404 errors

---

### 2. Real-time Notifications ⚠️ HIGH PRIORITY
**URLs Fixed:** 9 occurrences

**Test Steps:**
- [ ] Login to CRM dashboard
- [ ] Check notification bell icon
  - [ ] Should fetch from `/fetch-notification`
- [ ] Check message count
  - [ ] Should fetch from `/fetch-messages`
- [ ] Check waiting count badge
  - [ ] Should fetch from `/fetch-InPersonWaitingCount`
- [ ] Check activity count
  - [ ] Should fetch from `/fetch-TotalActivityCount`
- [ ] Check office visit notifications
  - [ ] Should fetch from `/fetch-office-visit-notifications`
- [ ] Click "View All Notifications"
  - [ ] Should redirect to `/all-notifications` (not `/admin/all-notifications`)
- [ ] Mark notification as seen
  - [ ] Should POST to `/mark-notification-seen`

**Expected Result:** All notification features work in real-time

---

### 3. Client Management ⚠️ HIGH PRIORITY
**URLs Fixed:** 11 occurrences

**Test Steps:**
- [ ] Go to client detail page (`/clients/detail/{id}`)
- [ ] Use client autocomplete dropdown
  - [ ] Should fetch from `/clients/get-allclients`
- [ ] Try to send email to client
  - [ ] Recipients dropdown should fetch from `/clients/get-recipients`
- [ ] Convert activity to note
  - [ ] Should POST to `/convert-activity-to-note`
- [ ] View task details
  - [ ] Should fetch from `/get-task-detail`
- [ ] View notes tab
  - [ ] Should fetch from `/get-notes`
- [ ] View activities tab
  - [ ] Should fetch from `/get-activities`
- [ ] View services tab
  - [ ] Should fetch from `/get-services`
- [ ] View appointments tab
  - [ ] Should fetch from `/get-appointments`
- [ ] View fees tab
  - [ ] Should fetch from `/get-all-fees`

**Expected Result:** All client management AJAX calls work

---

### 4. Appointment Booking Calendar
**URLs Fixed:** 6 occurrences

**Test Steps:**
- [ ] Navigate to Booking Calendar (`/booking/appointments`)
- [ ] Click on an appointment
  - [ ] Modal should open correctly
- [ ] Click "View Full Details" link
  - [ ] Should navigate to `/booking/appointments/{id}` (not `/admin/booking/...`)
- [ ] Update appointment status
  - [ ] Should POST to `/booking/appointments/{id}/update-status`
- [ ] Change consultant
  - [ ] Should POST to `/booking/appointments/{id}/update-consultant`

**Expected Result:** Calendar and appointment updates work

---

### 5. Document Management
**URLs Fixed:** 2 occurrences

**Test Steps:**
- [ ] Navigate to Documents/Signatures (`/signatures`)
- [ ] Select a document template
  - [ ] Should fetch templates from `/get-matter-templates`
- [ ] Send signing link
  - [ ] Should POST to `/documents/{id}/send-signing-link`
- [ ] View client matters (in signatures)
  - [ ] Should fetch from `/api/client-matters/{clientId}`

**Expected Result:** Document operations work without errors

---

### 6. Lead Management
**URLs Fixed:** 3 occurrences

**Test Steps:**
- [ ] Search for a lead
- [ ] Click on lead from search results
  - [ ] Should redirect to `/leads/history/{id}` (not `/admin/leads/history/`)

**Expected Result:** Lead navigation works correctly

---

### 7. Application Management
**URLs Fixed:** 11 occurrences

**Test Steps:**
- [ ] View application details
- [ ] Check application logs
  - [ ] Should fetch from `/get-applications-logs`
- [ ] Upload application document
  - [ ] Should POST to `/upload-document`

**Expected Result:** Application features work

---

### 8. AdminConsole - ANZSCO ⚠️ SPECIAL CASE
**URLs Fixed:** 2 occurrences (moved to `/adminconsole`)

**Test Steps:**
- [ ] Login as admin
- [ ] Navigate to AdminConsole → Database → ANZSCO
- [ ] Toggle occupation status
  - [ ] Should POST to `/adminconsole/anzsco/{id}/toggle-status` (not `/admin/anzsco/...`)
- [ ] Delete an ANZSCO entry
  - [ ] Should DELETE to `/adminconsole/anzsco/{id}`

**Expected Result:** ANZSCO admin functions work under `/adminconsole` prefix

---

## 🔍 Browser Console Checks

### Check for 404 Errors

1. **Open Developer Tools** (F12)
2. **Go to Network Tab**
3. **Filter:** XHR/Fetch
4. **Perform the test actions above**
5. **Look for:**
   - ❌ Any requests to `/admin/*` (except `/admin/login`, `/admin/logout`)
   - ❌ Any 404 status codes
   - ✅ All requests should be to root-level URLs or `/adminconsole/*`

### JavaScript Console

Check for errors:
```
- ❌ Failed to load resource: 404
- ❌ Uncaught (in promise) errors
- ❌ AJAX errors
```

---

## 📝 Testing by User Role

### Staff User Tests
- [ ] Login as regular staff
- [ ] Test office visits (check-in, attending, completing)
- [ ] Test notifications
- [ ] Test client operations
- [ ] Test lead management
- [ ] Test appointments

### Admin User Tests
- [ ] Login as admin
- [ ] All staff tests above
- [ ] AdminConsole → ANZSCO operations
- [ ] AdminConsole → User management
- [ ] AdminConsole → System settings

### Email Manager Tests
- [ ] Login to Email Manager (`/email_users/login`)
- [ ] Verify client selection dropdown works
  - [ ] Should still fetch from `/clients/get-allclients`

---

## 🚨 Critical Paths to Verify

### Path 1: Complete Office Visit Flow
```
1. Dashboard → Office Visits → Check In
2. Fill form → Submit (POST /checkin) ✅
3. View checkin detail (GET /get-checkin-detail) ✅
4. Update purpose (POST /update_visit_purpose) ✅
5. Attend session (POST /attend_session) ✅
6. Complete session (POST /complete_session) ✅
```

### Path 2: Client Communication Flow
```
1. Client Detail → Send Email
2. Load recipients (GET /clients/get-recipients) ✅
3. Select template (GET /get-matter-templates) ✅
4. Send email → Success
```

### Path 3: Appointment Management Flow
```
1. Booking Calendar → Click appointment
2. View full details (navigate to /booking/appointments/{id}) ✅
3. Update status (POST /booking/appointments/{id}/update-status) ✅
4. Change consultant (POST /booking/appointments/{id}/update-consultant) ✅
```

---

## 📊 Automated Verification

### Run These Commands

```bash
# 1. Check for any remaining /admin/ URLs (excluding login/logout)
Get-ChildItem -Path "resources\views" -Filter "*.blade.php" -Recurse | 
  Select-String -Pattern "'/admin/|`"/admin/|``/admin/" | 
  Where-Object { $_.Line -notmatch "login|logout|<!--.*-->" }

# Expected: Only commented-out code (4 occurrences in email_upload_test.blade.php)

# 2. Verify routes exist
php artisan route:list | findstr /i "get-checkin update_visit checkin booking/appointments convert-activity get-matter-templates"

# Expected: All routes should be listed

# 3. Clear caches (already done, but can repeat)
php artisan optimize:clear

# 4. Check for PHP errors
php artisan route:cache
# Expected: No errors
```

---

## ✅ Success Criteria

The fix is successful when:

- [ ] No 404 errors in browser console for CRM features
- [ ] All office visit operations complete successfully
- [ ] All notifications load in real-time
- [ ] Client autocomplete and recipient selection work
- [ ] Appointment calendar operations work
- [ ] Document template selection works
- [ ] Lead redirects work correctly
- [ ] ANZSCO admin functions work under `/adminconsole`
- [ ] No JavaScript errors related to failed AJAX calls
- [ ] All users can perform their daily tasks without errors

---

## 🐛 If Issues Are Found

### Debugging Steps

1. **Check Browser Console:**
   - Note the exact URL that's failing
   - Copy the full error message

2. **Verify Route Exists:**
   ```bash
   php artisan route:list | findstr "problem-url"
   ```

3. **Check if URL was missed:**
   ```bash
   # Search for the problematic URL in views
   Get-ChildItem -Path "resources\views" -Recurse | 
     Select-String -Pattern "problem-url"
   ```

4. **Check Laravel Logs:**
   ```
   storage/logs/laravel.log
   ```

5. **Report Issue:**
   - URL that failed
   - Expected route
   - Browser console error
   - Steps to reproduce

---

## 📞 Quick Reference

### URLs That Should Work (Root Level)
- ✅ `/dashboard`
- ✅ `/leads/*`
- ✅ `/clients/*`
- ✅ `/applications/*`
- ✅ `/appointments/*`
- ✅ `/booking/appointments/*`
- ✅ `/office-visits/*`
- ✅ `/signatures/*`
- ✅ `/documents/*`
- ✅ `/get-checkin-detail`
- ✅ `/fetch-notification`
- ✅ `/all-notifications`
- ✅ `/convert-activity-to-note`
- ✅ `/get-matter-templates`

### URLs That Should Stay Under /admin
- ✅ `/admin/login`
- ✅ `/admin/logout`

### URLs Under /adminconsole
- ✅ `/adminconsole/system/*`
- ✅ `/adminconsole/features/*`
- ✅ `/adminconsole/database/*`
- ✅ `/adminconsole/anzsco/*` ← Changed from `/admin/anzsco/`

### URLs Under /email_users
- ✅ `/email_users/login`
- ✅ `/email_users/*`

---

**Last Updated:** October 24, 2025  
**Status:** Ready for comprehensive testing  
**Estimated Testing Time:** 30-45 minutes for full checklist

