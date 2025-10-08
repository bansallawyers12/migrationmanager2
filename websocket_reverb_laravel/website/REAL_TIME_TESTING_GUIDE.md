# 🧪 Real-Time Message Testing Guide

## ✅ Test Page Successfully Updated!

Your test page at `http://127.0.0.1:8000/simple-websocket-test.html` now has **real-time message listening** capabilities!

---

## 🎯 New Features Added

### 📊 **Live Statistics Dashboard**
- **📨 Total Messages** - Auto-updates when new messages arrive
- **📡 Active Channels** - Shows how many channels you're subscribed to
- **⏱️ Session Time** - Tracks how long you've been connected

### 🎧 **Real-Time Message Listener**
- Subscribe to specific user's private channel
- Automatically receive messages in real-time
- Beautiful message cards with all details
- Sound notification on new message

### 💬 **Message Display**
- Shows sender name
- Message content
- Matter ID
- Message ID
- Timestamp
- Auto-scrolling to newest messages

---

## 🚀 How to Test (Step-by-Step)

### **Step 1: Start Your Servers**

Open **2 terminal windows**:

**Terminal 1 - Laravel Server:**
```bash
cd C:\xampp\htdocs\migration_manager_crm
php artisan serve
```

**Terminal 2 - Reverb WebSocket Server:**
```bash
cd C:\xampp\htdocs\migration_manager_crm
php artisan reverb:start
```

---

### **Step 2: Open the Test Page**

1. Open your browser
2. Go to: `http://127.0.0.1:8000/simple-websocket-test.html`
3. You should see the **Real-Time Message Listener** page

---

### **Step 3: Get Authentication Token**

Private channels require authentication. Get your Bearer token first:

**Using Postman:**
```http
POST http://127.0.0.1:8000/api/login
Content-Type: application/json

{
  "email": "your-email@example.com",
  "password": "your-password"
}
```

**Response will include:**
```json
{
  "success": true,
  "token": "1|abc123xyz456...",
  "user": {
    "id": 5,
    ...
  }
}
```

**Copy both:** 
- User ID (e.g., `5`)
- Token (e.g., `1|abc123xyz456...`)

📖 **Detailed guide:** See `GET_BEARER_TOKEN.md`

---

### **Step 4: Find a User ID to Test**

You need to know which user should receive messages. Check your database:

```sql
-- Find admin users
SELECT id, first_name, last_name, email, role FROM admins WHERE status = 1;

-- Find which users are assigned to a specific matter
SELECT 
    id,
    sel_migration_agent,
    sel_person_responsible,
    sel_person_assisting,
    client_unique_matter_no
FROM client_matters 
WHERE id = 1;  -- Change to your matter ID
```

**Example:** If User ID `5` is assigned to Matter ID `1`, use User ID `5`.

---

### **Step 5: Connect & Subscribe**

On the test page:

1. **Host:** `127.0.0.1` (already set)
2. **Port:** `8080` (already set)
3. **App Key:** `145cd98cfea9f69732ae6755ac889bcc` (already set)
4. **User ID:** Enter the user ID (e.g., `5`)
5. **Bearer Token:** Paste your token from Step 3 (e.g., `1|abc123xyz456...`)
6. Click **🚀 Connect & Subscribe**

**Expected Result:**
```
✅ Connected successfully!
📡 Subscribing to channel: private-user.5
✅ Successfully subscribed to private-user.5
```

**Status should show:**
```
Status: Subscribed to User 5 - Listening for messages...
```

---

### **Step 6: Send a Message via Postman**

Use the SAME token from Step 3:

```http
POST http://127.0.0.1:8000/api/messages/send
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "message": "Hello! Testing real-time chat from mobile app 🚀",
  "client_matter_id": 1
}
```

**IMPORTANT:** Make sure:
- The `client_matter_id` has User ID 5 assigned as:
  - Migration agent, OR
  - Person responsible, OR
  - Person assisting
- OR User ID 5 is a superadmin (role = 1)

---

### **Step 7: Watch the Magic! ✨**

**On the test page, you should INSTANTLY see:**

1. **📨 Total Messages counter increases** (0 → 1)
2. **New message card appears** with:
   - Sender name
   - Message content
   - Matter ID
   - Message ID
   - Timestamp
3. **🔔 Notification sound plays** (if browser allows)
4. **Connection Log shows:**
   ```
   📨 NEW MESSAGE RECEIVED!
   Message Data: {...}
   ```

---

## 🎨 What You'll See

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📨 Total Messages: 1        📡 Active Channels: 1
                  ⏱️ Session Time: 1:23
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📬 Real-Time Messages
┌─────────────────────────────────────────────┐
│ 📨 Message #1              🕐 2:30:45 PM    │
│                                             │
│ 💬 Hello! Testing real-time chat from      │
│    mobile app 🚀                            │
│                                             │
│ 👤 From: John Doe                           │
│ 📋 Matter ID: 1                             │
│ 🆔 Message ID: 123                          │
│ 📅 10/7/2025                                │
└─────────────────────────────────────────────┘
```

---

## 🔍 Troubleshooting

### ❌ **"Subscription Failed"**

**Problem:** Cannot subscribe to private channel

**Solutions:**
1. Check if Reverb server is running
2. Verify App Key matches your `.env` file
3. Private channels require authentication (currently using public subscription)

**Quick Fix:** For testing, we're using public subscription. In production, you'll need to add authentication.

---

### ❌ **No Message Received**

**Problem:** Message sent via API but not appearing on test page

**Check:**

1. **Verify User Assignment:**
   ```sql
   SELECT * FROM client_matters WHERE id = 1;
   ```
   Make sure User ID matches one of:
   - `sel_migration_agent`
   - `sel_person_responsible`
   - `sel_person_assisting`

2. **Check if User is Superadmin:**
   ```sql
   SELECT id, role FROM admins WHERE id = 5;
   ```
   If `role = 1`, user is superadmin and should receive all messages

3. **Check Reverb Server Logs:**
   Look at Terminal 2 (Reverb) for broadcast messages:
   ```
   [MessageSent] Broadcasting on private-user.5
   ```

4. **Check Browser Console:**
   - Open Developer Tools (F12)
   - Look for errors or WebSocket messages

---

### ❌ **Connection Timeout**

**Problem:** Cannot connect to WebSocket

**Solutions:**
1. Ensure Reverb server is running: `php artisan reverb:start`
2. Check port 8080 is not blocked by firewall
3. Verify `.env` has:
   ```
   REVERB_HOST=localhost
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

---

## 📋 Testing Checklist

Use this checklist to verify everything works:

- [ ] Laravel server running on port 8000
- [ ] Reverb server running on port 8080
- [ ] Test page opens at `http://127.0.0.1:8000/simple-websocket-test.html`
- [ ] User ID entered (e.g., 5)
- [ ] Click "Connect & Subscribe" - Status shows "Subscribed"
- [ ] Send message via Postman - Returns 201 status
- [ ] Message appears in database `messages` table
- [ ] Message counter updates (0 → 1)
- [ ] Message card displays on test page
- [ ] All message details are visible (sender, content, matter ID, etc.)
- [ ] Connection log shows "NEW MESSAGE RECEIVED!"
- [ ] Can send multiple messages and counter keeps increasing

---

## 🎯 Expected End-to-End Flow

```
Mobile App/Postman
       ↓
   REST API (/api/messages/send)
       ↓
   Database (messages table)
       ↓
   Laravel Event (MessageSent)
       ↓
   Reverb WebSocket Server
       ↓
   Broadcast to Channel (private-user.{userId})
       ↓
   Test Page Receives Event
       ↓
   Display Message + Update Counter ✅
```

---

## 💡 Pro Tips

1. **Keep Reverb Terminal Visible:** Watch for broadcast messages in real-time
2. **Use Multiple Browsers:** Test sender on one browser, receiver on another
3. **Test Different Users:** Try different User IDs to see who receives messages
4. **Check Matter Assignment:** Ensure matters have users assigned correctly
5. **Browser Console:** Keep F12 DevTools open to see all WebSocket events

---

## 🚀 Next Steps

Once this test page works perfectly:

1. ✅ **You've verified:** End-to-end real-time messaging works!
2. 🎯 **Ready to integrate:** Add this to your admin dashboard
3. 📱 **Mobile app:** Use same logic in Flutter app
4. 🌐 **Production:** Deploy with proper authentication and SSL

---

## 🆘 Need Help?

If messages still don't appear:
1. Check all servers are running
2. Verify database has correct user assignments
3. Look at Reverb server logs for errors
4. Check browser console for JavaScript errors
5. Ensure API returns 201 status when sending message

---

**Happy Testing! 🎉**

