# 📱 Mobile App Documentation

> **For Mobile App Developers:** Everything you need to integrate Laravel Reverb WebSocket in your mobile application.

---

## 📚 **Documentation Files**

### **📱 Main Integration Guide (Start Here)**

#### **1. MOBILE_APP_INTEGRATION_GUIDE.md**
- **Complete guide for mobile developers** (1,065 lines)
- Everything needed to integrate real-time chat
- Includes:
  - ✅ WebSocket configuration
  - ✅ Authentication flow (login, token storage)
  - ✅ Channel information (what to subscribe to)
  - ✅ WebSocket events (message.sent, message.updated, etc.)
  - ✅ REST API endpoints (all 7 endpoints with examples)
  - ✅ Flutter implementation guide
  - ✅ Data structures and JSON parsing
  - ✅ Security requirements
  - ✅ Complete testing guide
  - ✅ Troubleshooting (6 common issues)
- **Time:** 30-45 minutes
- **Read First:** Yes, this is your main resource

---

### **⚡ Quick Reference**

#### **2. MOBILE_QUICK_REFERENCE.md**
- Quick reference card (cheat sheet)
- Code snippets for copy-paste
- WebSocket configuration
- Channel subscriptions
- Event listeners
- API endpoints
- Troubleshooting table
- **Time:** 2 minutes
- **Use:** Daily reference, quick lookup

---

### **💻 Code Implementation**

#### **3. mobile-examples/flutter-realtime-chat.dart**
- **Complete Flutter implementation** (413 lines)
- Production-ready code
- Fully commented
- Includes:
  - ✅ WebSocket connection setup
  - ✅ Channel subscription
  - ✅ Event listeners
  - ✅ Message sending/receiving
  - ✅ Auto-reconnection
  - ✅ Error handling
  - ✅ UI examples
- **Time:** 20 minutes to read and adapt
- **Use:** Copy, paste, and customize for your app

---

## 🚀 **Getting Started**

### **Step 1: Read the Integration Guide**
```
MOBILE_APP_INTEGRATION_GUIDE.md (30-45 minutes)
```

**What you'll learn:**
- How WebSocket works with Laravel Reverb
- How to authenticate users
- What channels to subscribe to
- What events to listen for
- All API endpoints and usage
- Security best practices

---

### **Step 2: Review the Flutter Code**
```
mobile-examples/flutter-realtime-chat.dart (20 minutes)
```

**What you'll get:**
- Complete working implementation
- Code you can copy and adapt
- Best practices and patterns

---

### **Step 3: Use Quick Reference Daily**
```
MOBILE_QUICK_REFERENCE.md (2 minutes)
```

**What it provides:**
- Quick code snippets
- Configuration values
- API endpoints
- Quick troubleshooting

---

## 📋 **What You Need from Backend Team**

Before you start, get these from backend developer:

### **1. WebSocket Configuration**
```
Host: your-domain.com (or 127.0.0.1 for development)
Port: 8080
App Key: 145cd98cfea9f69732ae6755ac889bcc
App ID: 952b2edc3f42e289
```

### **2. API Base URL**
```
Development: http://127.0.0.1:8000/api
Production: https://your-domain.com/api
```

### **3. Test Credentials**
```
Email: test@example.com
Password: testpassword123
User ID: (ask backend team)
Matter ID: (ask backend team)
```

---

## 🔑 **Authentication Flow**

### **Quick Overview:**
```
1. Login → Get Bearer Token
2. Store Token Securely
3. Connect WebSocket (with token)
4. Subscribe to Channels (authenticated)
5. Listen for Events
6. Send/Receive Messages
```

**Detailed guide:** See `MOBILE_APP_INTEGRATION_GUIDE.md` Section 2

---

## 📡 **Channels & Events**

### **Channels to Subscribe:**
```dart
// User's private channel
channel = pusher.subscribe('private-user.$userId');

// Matter's private channel
channel = pusher.subscribe('private-matter.$matterId');
```

### **Events to Listen For:**
```dart
// 1. New message
channel.bind('message.sent', (event) { ... });

// 2. Message updated
channel.bind('message.updated', (event) { ... });

// 3. Unread count
channel.bind('unread.count.updated', (event) { ... });
```

**Complete details:** See `MOBILE_APP_INTEGRATION_GUIDE.md` Section 4

---

## 🔌 **REST API Endpoints**

### **All 7 Endpoints:**

1. **Login** - `POST /api/login`
2. **Send Message** - `POST /api/messages/send`
3. **Get Messages** - `GET /api/messages`
4. **Mark as Read** - `POST /api/messages/{id}/read`
5. **Get Unread Count** - `GET /api/messages/unread-count`
6. **Get Message Details** - `GET /api/messages/{id}`
7. **Logout** - `POST /api/logout`

**Complete documentation with examples:** See `MOBILE_APP_INTEGRATION_GUIDE.md` Section 5

---

## 📦 **Required Packages**

### **Flutter:**
```yaml
dependencies:
  laravel_echo: ^1.1.0
  pusher_client: ^2.0.0
  http: ^1.1.0
  flutter_secure_storage: ^9.0.0
  flutter_local_notifications: ^16.1.0
  provider: ^6.0.5
  intl: ^0.18.1
```

**Installation guide:** See `MOBILE_APP_INTEGRATION_GUIDE.md` Section 6

---

## 🧪 **Testing**

### **Before You Code:**
1. Ask backend team to test with web page first
2. Verify backend is working: `http://127.0.0.1:8000/simple-websocket-test.html`
3. Get test credentials from backend team

### **While Coding:**
1. Test login flow
2. Test WebSocket connection
3. Test channel subscription
4. Test event receiving
5. Test API calls
6. Test end-to-end message flow

**Complete testing guide:** See `MOBILE_APP_INTEGRATION_GUIDE.md` Section 9

---

## 🔧 **Common Issues**

| Issue | Solution | Details |
|-------|----------|---------|
| Connection failed | Check host, port, Reverb running | Section 10.1 |
| Auth error (404) | Verify auth endpoint `/api/broadcasting/auth` | Section 10.2 |
| Not receiving events | Check channel name and user assignment | Section 10.4 |
| 401 Unauthorized | Token expired, login again | Section 10.5 |

**Complete troubleshooting:** See `MOBILE_APP_INTEGRATION_GUIDE.md` Section 10

---

## 📊 **File Overview**

| File | Lines | Purpose | When to Use |
|------|-------|---------|-------------|
| MOBILE_APP_INTEGRATION_GUIDE.md | 1,065 | Complete guide | First time, reference |
| MOBILE_QUICK_REFERENCE.md | 177 | Quick reference | Daily use |
| mobile-examples/flutter-realtime-chat.dart | 413 | Implementation | When coding |

---

## 🎯 **Learning Path**

### **Day 1: Understanding**
```
1. Read MOBILE_APP_INTEGRATION_GUIDE.md (45 min)
   - Focus on Sections 1-4 (configuration, auth, events)
   
2. Review flutter-realtime-chat.dart (20 min)
   - Understand the code structure
   
3. Test with backend team (15 min)
   - Verify backend is working
```

### **Day 2: Implementation**
```
1. Setup Flutter project
   - Add required packages
   
2. Copy and adapt flutter-realtime-chat.dart
   - Modify for your app structure
   
3. Implement login flow
   - Store token securely
```

### **Day 3: Integration**
```
1. Connect WebSocket
2. Subscribe to channels
3. Listen for events
4. Test with backend
```

### **Daily: Reference**
```
Use MOBILE_QUICK_REFERENCE.md for quick lookup
```

---

## ✅ **Integration Checklist**

Before going live, verify:

- [ ] Can login and receive Bearer token
- [ ] Token is stored securely (flutter_secure_storage)
- [ ] WebSocket connects successfully
- [ ] Can subscribe to private channels
- [ ] Receive `message.sent` events in real-time
- [ ] Can send messages via API
- [ ] Messages appear in real-time on other devices
- [ ] Can mark messages as read
- [ ] Unread count updates correctly
- [ ] Handle connection loss and reconnection
- [ ] Handle token expiry gracefully
- [ ] Using HTTPS/WSS in production
- [ ] Error handling implemented
- [ ] Notifications working
- [ ] Tested with multiple users

---

## 🔗 **Related Documentation**

- **Backend Documentation:** See `../website/` folder
- **Documentation Index:** See `../INDEX.md`
- **Main README:** See `../README.md`

---

## 📞 **Need Help?**

### **Can't find something?**
1. Check `MOBILE_APP_INTEGRATION_GUIDE.md` table of contents
2. Use Ctrl+F to search within files
3. Check `MOBILE_QUICK_REFERENCE.md` for quick answers
4. Contact backend team for configuration values

### **Still stuck?**
- Review the Troubleshooting section in `MOBILE_APP_INTEGRATION_GUIDE.md`
- Check if backend is working with web test page
- Verify configuration values are correct

---

## 🎯 **What You'll Build**

After following these docs, your app will have:

- ✅ Real-time bidirectional messaging
- ✅ Instant message delivery (no polling)
- ✅ Message read receipts
- ✅ Unread count badges
- ✅ Automatic reconnection
- ✅ Secure authentication
- ✅ Production-ready code

---

## 🚀 **Quick Start Summary**

```
1. Read: MOBILE_APP_INTEGRATION_GUIDE.md
2. Code: Copy mobile-examples/flutter-realtime-chat.dart
3. Reference: Use MOBILE_QUICK_REFERENCE.md daily
4. Test: Follow testing guide in main documentation
```

---

**Start with `MOBILE_APP_INTEGRATION_GUIDE.md` - you'll have everything you need! 📱✨**

