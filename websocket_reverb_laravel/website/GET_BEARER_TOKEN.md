# 🔑 How to Get Bearer Token for Testing

## 📋 Quick Steps

### **Method 1: Using Postman (Recommended)**

#### **Step 1: Login via API**

```http
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
  "email": "your-email@example.com",
  "password": "your-password"
}
```

#### **Step 2: Copy the Token**

**Expected Response:**
```json
{
  "success": true,
  "token": "1|abc123xyz456...",
  "user": {
    "id": 5,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com"
  }
}
```

**Copy the token value:** `1|abc123xyz456...`

#### **Step 3: Use in Test Page**

1. Open `http://127.0.0.1:8000/simple-websocket-test.html`
2. Enter User ID: `5` (the ID from login response)
3. Paste Token: `1|abc123xyz456...`
4. Click "Connect & Subscribe"

---

### **Method 2: Using cURL**

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"your-email@example.com","password":"your-password"}'
```

Copy the token from the response.

---

### **Method 3: Check Database (Development Only)**

If you need to find existing tokens:

```sql
SELECT 
    pat.token,
    pat.tokenable_id as user_id,
    a.first_name,
    a.last_name,
    a.email,
    pat.created_at
FROM personal_access_tokens pat
JOIN admins a ON pat.tokenable_id = a.id
WHERE pat.tokenable_type = 'App\\Models\\Admin'
ORDER BY pat.created_at DESC
LIMIT 10;
```

**Note:** Tokens in database are hashed. You need to generate a new one via login.

---

## 🎯 Complete Testing Flow

### **1. Get Token (Postman)**

```http
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "token": "1|xyz789abc456def123",
  "user": {
    "id": 5,
    ...
  }
}
```

---

### **2. Open Test Page**

Go to: `http://127.0.0.1:8000/simple-websocket-test.html`

---

### **3. Fill in Details**

- **Host:** `127.0.0.1` ✅ (pre-filled)
- **Port:** `8080` ✅ (pre-filled)
- **App Key:** `145cd98cfea9f69732ae6755ac889bcc` ✅ (pre-filled)
- **User ID:** `5` (from login response)
- **Bearer Token:** `1|xyz789abc456def123` (from login response)

---

### **4. Connect**

Click **🚀 Connect & Subscribe**

**Expected Logs:**
```
🚀 Connecting to ws://127.0.0.1:8080/app/...
🔑 Using authentication with provided Bearer token
✅ Pusher initialized successfully
✅ Connected successfully!
📡 Subscribing to channel: private-user.5
✅ Successfully subscribed to private-user.5
```

**Status should show:**
```
Status: Subscribed to User 5 - Listening for messages...
```

---

### **5. Test Message**

In Postman, send a message using the SAME token:

```http
POST http://127.0.0.1:8000/api/messages/send
Authorization: Bearer 1|xyz789abc456def123
Content-Type: application/json

{
  "message": "Testing real-time with authentication! 🎉",
  "client_matter_id": 1
}
```

**Note:** Make sure User ID 5 is assigned to Matter ID 1 (as agent/responsible/assisting) or is a superadmin.

---

### **6. Watch Test Page**

You should instantly see:
- 📨 **Total Messages: 1**
- New message card appears
- Connection log shows "NEW MESSAGE RECEIVED!"

---

## ❓ Common Issues

### ❌ **"Invalid credentials"**

**Problem:** Login failed

**Solutions:**
1. Check email/password are correct
2. Verify user exists in `admins` table
3. Check user status is active (`status = 1`)

```sql
SELECT id, email, status FROM admins WHERE email = 'your-email@example.com';
```

---

### ❌ **"Unauthorized" when subscribing**

**Problem:** Token is invalid or expired

**Solutions:**
1. Generate a new token (login again)
2. Copy the entire token including the prefix (e.g., `1|...`)
3. Don't add extra "Bearer " in the token field (it's added automatically)

---

### ❌ **"Subscription error: AuthError"**

**Problem:** Authentication endpoint not working

**Check:**
1. Token is valid
2. `/api/broadcasting/auth` endpoint exists
3. Token matches the logged-in user

---

## 💡 Pro Tips

1. **Save Your Token:** Keep it in a text file for repeated testing
2. **Token Format:** Should look like `1|abc123xyz...` or just the hash part
3. **User ID Match:** Use the same User ID that the token belongs to
4. **Token Expiry:** Sanctum tokens don't expire by default, but can be configured to
5. **Multiple Tokens:** Each login creates a new token, old ones still work

---

## 🔒 Security Note

**For Production:**
- Don't hardcode tokens in frontend
- Use proper authentication flow
- Store tokens securely
- Implement token refresh mechanism
- Use HTTPS/WSS instead of HTTP/WS

**For Testing:**
- It's fine to paste tokens manually
- Tokens are for your local development only
- Don't commit tokens to git

---

## 🚀 Quick Copy-Paste

**Postman Login:**
```
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{"email":"admin@example.com","password":"password123"}
```

**Postman Send Message:**
```
POST http://127.0.0.1:8000/api/messages/send
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{"message":"Test message","client_matter_id":1}
```

---

**Now you're ready to test real-time messaging with authentication! 🎉**

