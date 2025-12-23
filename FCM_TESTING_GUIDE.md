# FCM Integration Testing Guide

This guide will help you test and verify that your FCM (Firebase Cloud Messaging) integration is working correctly.

## Prerequisites

1. ✅ Service Account JSON file downloaded from Firebase Console
2. ✅ JSON file saved to `storage/app/firebase-service-account.json`
3. ✅ `.env` file updated with `FCM_SERVICE_ACCOUNT_PATH=firebase-service-account.json`
4. ✅ User account with authentication token

---

## Step 1: Check Configuration

### 1.1 Verify Service Account File Exists

```bash
# Check if file exists
ls -la storage/app/firebase-service-account.json

# Or on Windows PowerShell:
Test-Path storage\app\firebase-service-account.json
```

**Expected:** File should exist and be readable

### 1.2 Verify .env Configuration

Check your `.env` file contains:
```env
FCM_SERVICE_ACCOUNT_PATH=firebase-service-account.json
```

### 1.3 Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Step 2: Test Token Registration

### 2.1 Register a Device Token

**Endpoint:** `POST /api/fcm/register-token`

**Headers:**
```
Authorization: Bearer YOUR_AUTH_TOKEN
Content-Type: application/json
```

**Request Body:**
```json
{
  "token": "your_fcm_device_token_here",
  "device_name": "Test Device",
  "device_type": "android"
}
```

**Using cURL:**
```bash
curl -X POST http://your-domain/api/fcm/register-token \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "your_fcm_device_token_here",
    "device_name": "Test Device",
    "device_type": "android"
  }'
```

**Expected Response (Success):**
```json
{
  "success": true,
  "message": "Device token registered successfully",
  "data": {
    "id": 1,
    "user_id": 123,
    "device_name": "Test Device",
    "device_type": "android",
    "is_active": true,
    "created_at": "2025-01-15T10:30:00.000000Z"
  }
}
```

**Expected Response (Error):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "token": ["The token field is required."]
  }
}
```

---

## Step 3: Test Notification Sending

### 3.1 Send Test Notification

**Endpoint:** `POST /api/fcm/test`

**Headers:**
```
Authorization: Bearer YOUR_AUTH_TOKEN
Content-Type: application/json
```

**Request Body (Optional):**
```json
{
  "user_id": 123,
  "title": "Test Notification",
  "body": "This is a test notification"
}
```

**Using cURL:**
```bash
curl -X POST http://your-domain/api/fcm/test \
  -H "Authorization: Bearer YOUR_AUTH_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Notification",
    "body": "This is a test notification"
  }'
```

**Expected Response (Success):**
```json
{
  "success": true,
  "message": "Test notification sent successfully",
  "data": {
    "user_id": 123,
    "device_tokens_count": 1,
    "title": "Test Notification",
    "body": "This is a test notification",
    "sent_at": "2025-01-15T10:30:00.000000Z"
  },
  "config_status": "working"
}
```

**Expected Response (No Tokens):**
```json
{
  "success": false,
  "message": "No active device tokens found for this user. Please register a device token first.",
  "user_id": 123,
  "device_tokens_count": 0,
  "config_status": "no_tokens"
}
```

**Expected Response (Config Error):**
```json
{
  "success": false,
  "message": "FCM service account file not found",
  "expected_path": "/path/to/storage/app/firebase-service-account.json",
  "config_status": "file_not_found"
}
```

---

## Step 4: Check Logs

### 4.1 View Laravel Logs

```bash
# View latest logs
tail -f storage/logs/laravel.log

# Or on Windows PowerShell:
Get-Content storage\logs\laravel.log -Tail 50 -Wait
```

**Look for:**
- ✅ `FCM device token registered` - Token registration successful
- ✅ `FCM access token` - Access token generated successfully
- ✅ `FCM v1 API request` - Notification sent
- ❌ `FCM service account file not found` - Configuration issue
- ❌ `Failed to get FCM access token` - Authentication issue
- ❌ `FCM notification failed` - Sending issue

### 4.2 Common Log Messages

**Success:**
```
[2025-01-15 10:30:00] local.INFO: FCM device token registered {"user_id":123,"device_token":"abc123...","device_name":"Test Device"}
[2025-01-15 10:30:01] local.INFO: FCM access token generated successfully
```

**Errors:**
```
[2025-01-15 10:30:00] local.ERROR: FCM service account file not found {"path":"/path/to/file"}
[2025-01-15 10:30:01] local.ERROR: Failed to get FCM access token {"response":"...","status":401}
```

---

## Step 5: Database Verification

### 5.1 Check Device Tokens Table

```sql
-- Check registered tokens
SELECT id, user_id, device_name, device_type, is_active, created_at 
FROM device_tokens 
WHERE user_id = YOUR_USER_ID;

-- Check active tokens
SELECT COUNT(*) as active_tokens 
FROM device_tokens 
WHERE is_active = 1 AND user_id = YOUR_USER_ID;
```

**Expected:** At least one active token for your test user

---

## Step 6: Test from Mobile App

### 6.1 Get FCM Token from Mobile App

1. Open your mobile app
2. Get the FCM registration token (this is device-specific)
3. Register it using the API endpoint from Step 2

### 6.2 Send Test Notification

1. Use the test endpoint from Step 3
2. Check your mobile device for the notification
3. Verify notification appears with correct title and body

---

## Troubleshooting

### Issue: "FCM service account file not found"

**Solution:**
1. Verify file exists: `ls storage/app/firebase-service-account.json`
2. Check file permissions (should be readable)
3. Verify `.env` has correct path: `FCM_SERVICE_ACCOUNT_PATH=firebase-service-account.json`
4. Clear config cache: `php artisan config:clear`

### Issue: "Failed to get FCM access token"

**Solution:**
1. Verify service account JSON is valid JSON
2. Check that `project_id` exists in JSON
3. Verify `private_key` is present and correct
4. Check that service account has "Firebase Cloud Messaging Admin" role
5. Verify internet connection (needs to reach Google OAuth)

### Issue: "No active device tokens found"

**Solution:**
1. Register a device token first using `/api/fcm/register-token`
2. Verify token is active in database: `SELECT * FROM device_tokens WHERE user_id = ?`
3. Check token wasn't deactivated due to errors

### Issue: "Notification sent but not received on device"

**Solution:**
1. Verify device token is correct (not expired)
2. Check device has internet connection
3. Verify app has notification permissions enabled
4. Check Firebase Console for delivery status
5. Verify app is in foreground/background correctly handling notifications

### Issue: "Invalid token" errors

**Solution:**
1. Token may be expired - register a new token
2. App may have been uninstalled/reinstalled
3. Token format may be incorrect
4. Check logs for specific error code

---

## Quick Test Checklist

- [ ] Service account JSON file exists
- [ ] `.env` configured correctly
- [ ] Config cache cleared
- [ ] Device token registered successfully
- [ ] Test notification endpoint returns success
- [ ] Logs show no errors
- [ ] Database has active token
- [ ] Mobile device receives notification

---

## Testing with Postman

### Import Collection

1. Create a new Postman collection
2. Add environment variables:
   - `base_url`: Your API base URL
   - `auth_token`: Your authentication token
   - `fcm_token`: Your FCM device token

### Test Requests

1. **Register Token**
   - Method: POST
   - URL: `{{base_url}}/api/fcm/register-token`
   - Headers: `Authorization: Bearer {{auth_token}}`
   - Body: JSON with `token`, `device_name`, `device_type`

2. **Test Notification**
   - Method: POST
   - URL: `{{base_url}}/api/fcm/test`
   - Headers: `Authorization: Bearer {{auth_token}}`
   - Body: JSON with `title`, `body` (optional)

3. **Unregister Token**
   - Method: POST
   - URL: `{{base_url}}/api/fcm/unregister-token`
   - Headers: `Authorization: Bearer {{auth_token}}`
   - Body: JSON with `token`

---

## Success Indicators

✅ **Configuration Working:**
- Service account file loads without errors
- Access token generated successfully
- No errors in logs

✅ **Token Registration Working:**
- Token saved to database
- Returns success response
- Token appears in `device_tokens` table

✅ **Notification Sending Working:**
- Test endpoint returns success
- No errors in logs
- Notification received on device (if testing with real device)

---

## Need Help?

Check these resources:
- Laravel logs: `storage/logs/laravel.log`
- Database: `device_tokens` table
- Firebase Console: Check delivery status
- FCM Documentation: https://firebase.google.com/docs/cloud-messaging

