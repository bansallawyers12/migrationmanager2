# 📱 Mobile App Integration Guide - Laravel Reverb WebSocket

> **For Mobile App Developers:** This document contains everything you need to integrate real-time chat using Laravel Reverb WebSocket in your mobile application.

---

## 🎯 **Quick Overview**

This backend provides real-time bidirectional messaging through:
- **WebSocket Server:** Laravel Reverb
- **Authentication:** Bearer Token (Sanctum)
- **Channels:** Private user and matter channels
- **Events:** Real-time message notifications

---

## 📋 **Table of Contents**

1. [WebSocket Configuration](#1-websocket-configuration)
2. [Authentication Flow](#2-authentication-flow)
3. [Channel Information](#3-channel-information)
4. [WebSocket Events](#4-websocket-events)
5. [REST API Endpoints](#5-rest-api-endpoints)
6. [Flutter Implementation](#6-flutter-implementation)
7. [Data Structures](#7-data-structures)
8. [Security Requirements](#8-security-requirements)
9. [Testing Guide](#9-testing-guide)
10. [Troubleshooting](#10-troubleshooting)

---

## 1. 🔧 **WebSocket Configuration**

### **Connection Details**

```
WebSocket Host: 127.0.0.1 (development) / your-domain.com (production)
WebSocket Port: 8080
WebSocket Scheme: ws (development) / wss (production)
App Key: 145cd98cfea9f69732ae6755ac889bcc
App ID: 952b2edc3f42e289
```

### **Authentication Endpoint**

```
URL: https://your-domain.com/api/broadcasting/auth
Method: POST
Headers:
  - Authorization: Bearer {token}
  - Accept: application/json
  - Content-Type: application/json
```

### **Flutter Configuration Example**

```dart
final options = PusherOptions(
  host: '127.0.0.1',  // Use your domain in production
  wsPort: 8080,
  encrypted: false,   // Set to true in production (wss)
  auth: PusherAuth(
    'https://your-domain.com/api/broadcasting/auth',
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    },
  ),
);
```

---

## 2. 🔑 **Authentication Flow**

### **Step 1: User Login**

Before connecting to WebSocket, user must login to get Bearer token.

**Endpoint:** `POST /api/login`

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "token": "1|abc123xyz456def789...",
  "user": {
    "id": 5,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "role": 2,
    "status": 1
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### **Step 2: Store Token**

```dart
// Store securely using flutter_secure_storage
final storage = FlutterSecureStorage();
await storage.write(key: 'auth_token', value: token);
await storage.write(key: 'user_id', value: userId.toString());
```

### **Step 3: Use Token**

Use this token for:
1. All REST API requests (Authorization header)
2. WebSocket authentication (Auth endpoint)

---

## 3. 📡 **Channel Information**

### **Channel Types & Naming Convention**

#### **User Private Channel**
```
Channel Name: private-user.{userId}
Example: private-user.5
Type: Private (requires authentication)
Purpose: Receive messages sent to this specific user
```

**When to Subscribe:**
- After successful login
- Use logged-in user's ID
- Example: If user ID is 5, subscribe to `private-user.5`

#### **Matter Private Channel**
```
Channel Name: private-matter.{matterId}
Example: private-matter.123
Type: Private (requires authentication)
Purpose: Receive messages for a specific matter/case
```

**When to Subscribe:**
- When viewing a specific matter/case
- Use matter ID from API
- Multiple users can subscribe to same matter channel

### **Subscribe Example (Flutter)**

```dart
// Subscribe to user's private channel
final userId = 5;
final channel = pusher.subscribe('private-user.$userId');

// Listen for events
channel.bind('message.sent', (event) {
  final data = jsonDecode(event.data);
  handleNewMessage(data);
});
```

---

## 4. 📨 **WebSocket Events**

### **Event 1: message.sent**

Triggered when a new message is sent.

**Channel:** `private-user.{userId}`  
**Event Name:** `message.sent`

**Data Structure:**
```json
{
  "message": {
    "id": 123,
    "message": "Hello from admin!",
    "sender": "John Doe",
    "sender_id": 5,
    "recipient_id": null,
    "sent_at": "2025-10-07T14:30:45.000000Z",
    "is_read": false,
    "client_matter_id": 1
  },
  "timestamp": "2025-10-07T14:30:45.000000Z",
  "type": "message_sent"
}
```

**How to Handle:**
```dart
channel.bind('message.sent', (event) {
  final data = jsonDecode(event.data);
  final message = data['message'];
  
  // Update UI with new message
  setState(() {
    messages.insert(0, Message.fromJson(message));
    totalMessages++;
  });
  
  // Show notification
  showNotification('New message from ${message['sender']}');
  
  // Play sound
  playNotificationSound();
});
```

---

### **Event 2: message.updated**

Triggered when a message is marked as read or updated.

**Channel:** `private-user.{userId}`  
**Event Name:** `message.updated`

**Data Structure:**
```json
{
  "message": {
    "id": 123,
    "message": "Hello from admin!",
    "sender": "John Doe",
    "recipient": "Jane Smith",
    "is_read": true,
    "read_at": "2025-10-07T14:35:00.000000Z",
    "sent_at": "2025-10-07T14:30:45.000000Z"
  },
  "timestamp": "2025-10-07T14:35:00.000000Z",
  "type": "message_updated"
}
```

**How to Handle:**
```dart
channel.bind('message.updated', (event) {
  final data = jsonDecode(event.data);
  final message = data['message'];
  
  // Update message status in list
  setState(() {
    final index = messages.indexWhere((m) => m.id == message['id']);
    if (index != -1) {
      messages[index].isRead = true;
      messages[index].readAt = message['read_at'];
    }
  });
});
```

---

### **Event 3: unread.count.updated**

Triggered when unread message count changes.

**Channel:** `private-user.{userId}`  
**Event Name:** `unread.count.updated`

**Data Structure:**
```json
{
  "user_id": 5,
  "unread_count": 3,
  "timestamp": "2025-10-07T14:30:45.000000Z"
}
```

**How to Handle:**
```dart
channel.bind('unread.count.updated', (event) {
  final data = jsonDecode(event.data);
  
  // Update badge count
  setState(() {
    unreadCount = data['unread_count'];
  });
  
  // Update app badge
  FlutterAppBadger.updateBadgeCount(unreadCount);
});
```

---

## 5. 🔌 **REST API Endpoints**

### **Base URLs**

```
Development: http://127.0.0.1:8000/api
Production: https://your-domain.com/api
```

### **Authentication Header**

All endpoints require Bearer token:
```
Authorization: Bearer 1|abc123xyz456...
```

---

### **5.1 Login**

**Endpoint:** `POST /api/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Success Response (200):**
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

---

### **5.2 Send Message**

**Endpoint:** `POST /api/messages/send`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "message": "Hello from mobile app!",
  "client_matter_id": 1
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Message sent successfully",
  "data": {
    "message_id": 123,
    "message": {
      "id": 123,
      "message": "Hello from mobile app!",
      "sender": "John Doe",
      "sender_id": 5,
      "client_matter_id": 1,
      "sent_at": "2025-10-07T14:30:45.000000Z"
    },
    "sent_at": "2025-10-07T14:30:45.000000Z"
  }
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "message": ["The message field is required."],
    "client_matter_id": ["The client matter id field is required."]
  }
}
```

---

### **5.3 Get Messages**

**Endpoint:** `GET /api/messages`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Query Parameters:**
```
client_matter_id: 1 (required)
page: 1 (optional, default: 1)
limit: 20 (optional, default: 20, max: 100)
```

**Example Request:**
```
GET /api/messages?client_matter_id=1&page=1&limit=20
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "messages": [
      {
        "id": 123,
        "message": "Hello!",
        "sender": "John Doe",
        "recipient": "Jane Smith",
        "sender_id": 5,
        "recipient_id": 10,
        "is_sender": true,
        "is_recipient": false,
        "sent_at": "2025-10-07T14:30:45.000000Z",
        "read_at": null,
        "is_read": false,
        "client_matter_id": 1,
        "created_at": "2025-10-07T14:30:45.000000Z",
        "updated_at": "2025-10-07T14:30:45.000000Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 45,
      "last_page": 3
    },
    "filters": {
      "client_matter_id": 1
    }
  }
}
```

---

### **5.4 Mark Message as Read**

**Endpoint:** `POST /api/messages/{id}/read`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Example Request:**
```
POST /api/messages/123/read
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Message marked as read"
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Message not found"
}
```

**Error Response (403):**
```json
{
  "success": false,
  "message": "You are not authorized for mark as read"
}
```

---

### **5.5 Get Unread Count**

**Endpoint:** `GET /api/messages/unread-count`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "unread_count": 5
  }
}
```

---

### **5.6 Get Message Details**

**Endpoint:** `GET /api/messages/{id}`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Example Request:**
```
GET /api/messages/123
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "message": "Hello!",
    "sender": "John Doe",
    "recipient": "Jane Smith",
    "sender_id": 5,
    "recipient_id": 10,
    "is_sender": true,
    "is_recipient": false,
    "sent_at": "2025-10-07T14:30:45.000000Z",
    "read_at": "2025-10-07T14:35:00.000000Z",
    "is_read": true,
    "client_matter_id": 1,
    "created_at": "2025-10-07T14:30:45.000000Z",
    "updated_at": "2025-10-07T14:35:00.000000Z"
  }
}
```

---

### **5.7 Logout**

**Endpoint:** `POST /api/logout`

**Headers:**
```
Authorization: Bearer {token}
Accept: application/json
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## 6. 📱 **Flutter Implementation**

### **Required Packages**

Add to `pubspec.yaml`:

```yaml
dependencies:
  flutter:
    sdk: flutter
  
  # WebSocket / Real-time
  laravel_echo: ^1.1.0
  pusher_client: ^2.0.0
  
  # HTTP / API
  http: ^1.1.0
  dio: ^5.3.0  # Alternative to http
  
  # State Management
  provider: ^6.0.5  # or riverpod, bloc, etc.
  
  # Storage
  flutter_secure_storage: ^9.0.0
  
  # Notifications
  flutter_local_notifications: ^16.1.0
  
  # Utilities
  intl: ^0.18.1  # Date formatting
```

### **Complete Implementation**

See the complete Flutter implementation in:
```
websocket_reverb_laravel/mobile-examples/flutter-realtime-chat.dart
```

This file includes:
- ✅ WebSocket connection setup
- ✅ Channel subscription
- ✅ Event listeners
- ✅ Message sending/receiving
- ✅ Auto-reconnection
- ✅ Error handling
- ✅ UI examples

---

## 7. 📊 **Data Structures**

### **User Object**

```dart
class User {
  final int id;
  final String firstName;
  final String lastName;
  final String email;
  final int role;
  final int status;
  
  String get fullName => '$firstName $lastName';
}
```

### **Message Object**

```dart
class Message {
  final int id;
  final String message;
  final String? sender;
  final String? recipient;
  final int senderId;
  final int? recipientId;
  final DateTime sentAt;
  final DateTime? readAt;
  final bool isRead;
  final int clientMatterId;
  final DateTime createdAt;
  final DateTime updatedAt;
  
  bool isMine(int currentUserId) => senderId == currentUserId;
}
```

### **Example JSON Parsing**

```dart
Message messageFromJson(Map<String, dynamic> json) {
  return Message(
    id: json['id'],
    message: json['message'],
    sender: json['sender'],
    recipient: json['recipient'],
    senderId: json['sender_id'],
    recipientId: json['recipient_id'],
    sentAt: DateTime.parse(json['sent_at']),
    readAt: json['read_at'] != null ? DateTime.parse(json['read_at']) : null,
    isRead: json['is_read'] ?? false,
    clientMatterId: json['client_matter_id'],
    createdAt: DateTime.parse(json['created_at']),
    updatedAt: DateTime.parse(json['updated_at']),
  );
}
```

---

## 8. 🔒 **Security Requirements**

### **Token Storage**

✅ **DO:**
- Use `flutter_secure_storage` for token storage
- Clear token on logout
- Validate token before each request

❌ **DON'T:**
- Store token in SharedPreferences (not secure)
- Store token in plain text files
- Log token to console in production

```dart
// Good
final storage = FlutterSecureStorage();
await storage.write(key: 'auth_token', value: token);
final token = await storage.read(key: 'auth_token');
await storage.delete(key: 'auth_token'); // On logout

// Bad
SharedPreferences prefs = await SharedPreferences.getInstance();
prefs.setString('token', token); // ❌ Not secure
```

---

### **HTTPS/WSS in Production**

✅ **Production:**
```dart
final options = PusherOptions(
  host: 'your-domain.com',
  wsPort: 443,
  encrypted: true,  // ✅ Use WSS (secure)
  // ...
);
```

❌ **Don't use in production:**
```dart
encrypted: false,  // ❌ Only for development
```

---

### **Handle Token Expiry**

```dart
Future<void> apiCall() async {
  try {
    final response = await http.get(url, headers: headers);
    
    if (response.statusCode == 401) {
      // Token expired or invalid
      await logout();
      navigateToLogin();
    }
  } catch (e) {
    handleError(e);
  }
}
```

---

### **SSL Certificate Validation**

✅ **Production:**
```dart
// Use default SSL validation
final client = HttpClient();
// Don't override certificate validation
```

❌ **Never do this in production:**
```dart
// ❌ DANGEROUS - Only for development with self-signed certs
HttpOverrides.global = MyHttpOverrides();
```

---

## 9. 🧪 **Testing Guide**

### **Step 1: Test with Web Page First**

Before integrating in mobile app, verify backend is working:

1. Open: `http://127.0.0.1:8000/simple-websocket-test.html`
2. Login via Postman to get token
3. Enter User ID and Bearer Token
4. Connect and send test message
5. Verify message appears in real-time

**If this works, backend is ready!**

---

### **Step 2: Test Credentials**

Use these credentials for testing:

```
Base URL (Development): http://127.0.0.1:8000/api
WebSocket Host: 127.0.0.1
WebSocket Port: 8080

Test User:
Email: test@example.com
Password: testpassword123
User ID: 5

Test Matter ID: 1
```

---

### **Step 3: Test API Endpoints**

Use Postman to test all endpoints:

1. **Login:** Get token ✅
2. **Get Messages:** Fetch message history ✅
3. **Send Message:** Post a new message ✅
4. **Mark as Read:** Update message status ✅
5. **Get Unread Count:** Check badge count ✅

---

### **Step 4: Test WebSocket Connection**

```dart
// Test connection
void testWebSocket() {
  try {
    final pusher = PusherClient(appKey, options);
    
    pusher.onConnectionStateChange((state) {
      print('Connection State: ${state?.currentState}');
      
      if (state?.currentState == 'CONNECTED') {
        print('✅ WebSocket connected successfully!');
      }
    });
    
    pusher.connect();
  } catch (e) {
    print('❌ Connection failed: $e');
  }
}
```

---

### **Step 5: Test Channel Subscription**

```dart
void testChannelSubscription() {
  final channel = pusher.subscribe('private-user.5');
  
  channel.bind('pusher:subscription_succeeded', (event) {
    print('✅ Successfully subscribed to channel');
  });
  
  channel.bind('pusher:subscription_error', (event) {
    print('❌ Subscription failed: ${event?.data}');
  });
}
```

---

### **Step 6: Test Event Receiving**

```dart
void testEventReceiving() {
  channel.bind('message.sent', (event) {
    print('✅ Received message event!');
    print('Data: ${event?.data}');
  });
  
  // Now send a message via Postman
  // You should see the event received here
}
```

---

## 10. 🔧 **Troubleshooting**

### **Issue 1: Connection Failed**

**Error:** `Connection timeout` or `Failed to connect`

**Solutions:**
1. Verify Reverb server is running: `php artisan reverb:start`
2. Check host and port are correct
3. In development, use `ws://` not `wss://`
4. Check firewall isn't blocking port 8080

---

### **Issue 2: Authentication Error (404)**

**Error:** `Unable to retrieve auth string from channel-authorization endpoint`

**Solutions:**
1. Verify auth endpoint: `/api/broadcasting/auth`
2. Check Bearer token is valid
3. Ensure token is sent in Authorization header
4. Verify `Accept: application/json` header is present

**Check token:**
```dart
print('Token: $token'); // Should be like: 1|abc123...
print('Auth URL: ${options.auth?.endpoint}');
```

---

### **Issue 3: Subscription Failed**

**Error:** `Subscription error` or `pusher:subscription_error`

**Solutions:**
1. Ensure channel name is correct: `private-user.{userId}`
2. Verify user ID matches the logged-in user
3. Check token belongs to the user you're subscribing for
4. Make sure user is authorized to access this channel

---

### **Issue 4: Not Receiving Events**

**Problem:** Connected and subscribed but no events received

**Check:**
1. Are you listening to correct event name? `message.sent`
2. Is message being sent to correct user?
3. Check if user is assigned to the matter
4. Verify backend is broadcasting (check Reverb logs)

**Debug:**
```dart
// Listen to ALL events for debugging
channel.bind_global((event) {
  print('Event received: ${event?.eventName}');
  print('Data: ${event?.data}');
});
```

---

### **Issue 5: Token Expired**

**Error:** `401 Unauthorized` from API

**Solution:**
```dart
if (response.statusCode == 401) {
  // Clear stored token
  await storage.delete(key: 'auth_token');
  
  // Redirect to login
  Navigator.pushNamedAndRemoveUntil(
    context, 
    '/login', 
    (route) => false
  );
}
```

---

### **Issue 6: Messages Not Sending**

**Error:** `422 Validation Error` or `500 Server Error`

**Check:**
1. Verify `client_matter_id` is provided and valid
2. Check message is not empty (unless allowed)
3. Ensure Bearer token is valid
4. Check API endpoint is correct: `/api/messages/send`

---

## 📞 **Support & Contact**

### **Backend Developer Contact**

```
Name: [Your Name]
Email: [Your Email]
Support Hours: [Your Hours]
```

### **Documentation**

- **Complete Guide:** `LARAVEL_REVERB_REALTIME_CHAT_GUIDE.md`
- **Testing Guide:** `REAL_TIME_TESTING_GUIDE.md`
- **Authentication:** `GET_BEARER_TOKEN.md`
- **Flutter Example:** `mobile-examples/flutter-realtime-chat.dart`

### **Test Page**

Web test page for verification:
```
http://127.0.0.1:8000/simple-websocket-test.html
```

---

## ✅ **Integration Checklist**

Before going live, verify:

- [ ] Can login and receive Bearer token
- [ ] Token is stored securely
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

## 🚀 **Quick Start Summary**

1. **Get Token:** `POST /api/login` → Store token
2. **Connect WebSocket:** Use token for auth
3. **Subscribe Channel:** `private-user.{userId}`
4. **Listen Events:** `message.sent`, `message.updated`, etc.
5. **Send Messages:** `POST /api/messages/send`
6. **Update UI:** Display messages in real-time

---

## 📦 **Files Included for Mobile Developer**

```
websocket_reverb_laravel/
├── MOBILE_APP_INTEGRATION_GUIDE.md       ← This file
├── LARAVEL_REVERB_REALTIME_CHAT_GUIDE.md ← Complete reference
├── REAL_TIME_TESTING_GUIDE.md            ← Testing instructions
├── GET_BEARER_TOKEN.md                   ← Authentication guide
├── mobile-examples/
│   └── flutter-realtime-chat.dart        ← Full Flutter implementation
└── README.md                              ← Overview
```

---

**You're all set! If you have questions, refer to the documentation or contact the backend team. Happy coding! 🎉📱**

