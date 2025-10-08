# 📱 Mobile App Integration - Quick Reference Card

> **Quick reference for mobile developers.** For complete details, see `MOBILE_APP_INTEGRATION_GUIDE.md`

---

## 🔧 **WebSocket Configuration**

```dart
final options = PusherOptions(
  host: '127.0.0.1',              // your-domain.com in production
  wsPort: 8080,
  encrypted: false,                // true in production (wss)
  auth: PusherAuth(
    'https://your-domain.com/api/broadcasting/auth',
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  ),
);

final pusher = PusherClient('145cd98cfea9f69732ae6755ac889bcc', options);
```

---

## 🔑 **Authentication**

### Login to Get Token
```dart
POST /api/login
{
  "email": "user@example.com",
  "password": "password123"
}

Response: {
  "token": "1|abc123...",
  "user": { "id": 5, ... }
}
```

---

## 📡 **Channels to Subscribe**

```dart
// Subscribe to user's messages
final channel = pusher.subscribe('private-user.$userId');

// Subscribe to matter's messages
final channel = pusher.subscribe('private-matter.$matterId');
```

---

## 📨 **Events to Listen For**

### 1. New Message
```dart
channel.bind('message.sent', (event) {
  final data = jsonDecode(event.data);
  // Handle new message
});
```

### 2. Message Updated
```dart
channel.bind('message.updated', (event) {
  final data = jsonDecode(event.data);
  // Update message status
});
```

### 3. Unread Count
```dart
channel.bind('unread.count.updated', (event) {
  final data = jsonDecode(event.data);
  // Update badge count
});
```

---

## 🔌 **REST API Endpoints**

### Send Message
```dart
POST /api/messages/send
Authorization: Bearer {token}

{
  "message": "Hello!",
  "client_matter_id": 1
}
```

### Get Messages
```dart
GET /api/messages?client_matter_id=1&page=1&limit=20
Authorization: Bearer {token}
```

### Mark as Read
```dart
POST /api/messages/{id}/read
Authorization: Bearer {token}
```

### Get Unread Count
```dart
GET /api/messages/unread-count
Authorization: Bearer {token}
```

---

## 📦 **Required Packages**

```yaml
dependencies:
  laravel_echo: ^1.1.0
  pusher_client: ^2.0.0
  http: ^1.1.0
  flutter_secure_storage: ^9.0.0
```

---

## 🎯 **Complete Flow**

```
1. Login → Get Token
2. Connect WebSocket with Token
3. Subscribe to private-user.{userId}
4. Listen for events
5. Send/receive messages in real-time
```

---

## 📚 **Full Documentation**

- **Complete Guide:** `MOBILE_APP_INTEGRATION_GUIDE.md` ← Start here!
- **Flutter Code:** `mobile-examples/flutter-realtime-chat.dart`
- **Authentication:** `GET_BEARER_TOKEN.md`
- **Testing:** `REAL_TIME_TESTING_GUIDE.md`

---

## 🆘 **Quick Troubleshooting**

| Issue | Solution |
|-------|----------|
| Connection failed | Check host, port, and Reverb is running |
| Auth error (404) | Verify `/api/broadcasting/auth` endpoint |
| Not receiving events | Check channel name and user assignment |
| 401 Unauthorized | Token expired, login again |

---

## ✅ **Test Credentials**

```
API URL: http://127.0.0.1:8000/api
WebSocket: ws://127.0.0.1:8080
App Key: 145cd98cfea9f69732ae6755ac889bcc

Test User: test@example.com / testpassword123
```

---

**Need help? Check `MOBILE_APP_INTEGRATION_GUIDE.md` for complete details!**

