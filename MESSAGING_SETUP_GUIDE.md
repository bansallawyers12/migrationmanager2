# Real-time Messaging Setup Guide

## 🚀 **Complete Implementation Steps**

### **1. Environment Configuration**

Add these variables to your `.env` file:

```env
# Broadcasting Configuration
BROADCAST_DRIVER=pusher

# Pusher Configuration (for real-time messaging)
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-pusher-key
PUSHER_APP_SECRET=your-pusher-secret
PUSHER_APP_CLUSTER=ap2

# For local development, you can use these values:
# PUSHER_APP_ID=local
# PUSHER_APP_KEY=local
# PUSHER_APP_SECRET=local
# PUSHER_APP_CLUSTER=local

# WebSocket Server Configuration (if using Laravel WebSockets)
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http

# Queue Configuration (for message processing)
QUEUE_CONNECTION=database

# Cache Configuration (for message caching)
CACHE_DRIVER=file
```

### **2. Database Migration**

The messaging system uses the existing `messages` table. If it doesn't exist, create it:

```sql
CREATE TABLE messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255),
    message TEXT,
    sender VARCHAR(255),
    recipient VARCHAR(255),
    sender_id BIGINT UNSIGNED,
    recipient_id BIGINT UNSIGNED,
    sent_at TIMESTAMP NULL,
    read_at TIMESTAMP NULL,
    read BOOLEAN DEFAULT FALSE,
    message_type ENUM('urgent', 'important', 'normal', 'low_priority') DEFAULT 'normal',
    matter_id BIGINT UNSIGNED,
    task_id BIGINT UNSIGNED,
    attachments JSON,
    metadata JSON,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (sender_id) REFERENCES admins(id),
    FOREIGN KEY (recipient_id) REFERENCES admins(id)
);
```

### **3. Clear Laravel Cache**

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### **4. Test the Implementation**

#### **API Endpoints Available:**

1. **Send Message**: `POST /api/messages/send`
2. **Get Messages**: `GET /api/messages`
3. **Get Message Details**: `GET /api/messages/{id}`
4. **Mark as Read**: `PUT /api/messages/{id}/read`
5. **Delete Message**: `DELETE /api/messages/{id}`
6. **Get Unread Count**: `GET /api/messages/unread-count`
7. **Get Recipients**: `GET /api/messages/recipients`

#### **Test with Postman:**

```json
// Send Message
POST /api/messages/send
Headers:
- Authorization: Bearer {your-token}
- Content-Type: application/json

Body:
{
    "recipient_id": 2,
    "subject": "Test Message",
    "message": "This is a test message",
    "message_type": "normal"
}
```

### **5. Website Integration**

#### **Include in your website:**

1. **Add to your layout file:**
```html
<!-- Include Pusher JS -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<!-- Include messaging script -->
<script src="/js/realtime-messaging.js"></script>

<!-- Add user ID meta tag -->
<meta name="user-id" content="{{ auth()->id() }}">
<meta name="auth-token" content="{{ auth()->user()->createToken('messaging')->plainTextToken }}">
```

2. **Or use the complete integration template:**
```php
// In your controller
return view('messaging.integration');
```

### **6. Mobile App Integration**

#### **Flutter/Dart Implementation:**

```dart
// 1. Add dependencies to pubspec.yaml
dependencies:
  pusher_channels_flutter: ^2.2.1
  http: ^1.1.0

// 2. Initialize Pusher
class MessagingService {
  late PusherChannelsFlutter pusher;
  
  void initializePusher(String token) {
    pusher = PusherChannelsFlutter.getInstance();
    
    await pusher.init(
      apiKey: "your-pusher-key",
      cluster: "ap2",
      authEndpoint: "https://your-domain.com/api/broadcasting/auth",
      auth: {
        "headers": {
          "Authorization": "Bearer $token",
        }
      }
    );
    
    // Subscribe to user channel
    await pusher.subscribe(
      channelName: "private-user.$userId",
      onEvent: (event) {
        if (event.eventName == "message.sent") {
          // Handle new message
          handleNewMessage(event.data);
        }
      }
    );
  }
  
  // Send message
  Future<void> sendMessage({
    required int recipientId,
    required String subject,
    required String message,
    String messageType = 'normal',
  }) async {
    final response = await http.post(
      Uri.parse('https://your-domain.com/api/messages/send'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'recipient_id': recipientId,
        'subject': subject,
        'message': message,
        'message_type': messageType,
      }),
    );
    
    if (response.statusCode == 201) {
      // Message sent successfully
    }
  }
}
```

### **7. Real-time Features**

#### **Website Features:**
- ✅ **Real-time message delivery**
- ✅ **Browser notifications**
- ✅ **Unread message counter**
- ✅ **Message status updates**
- ✅ **Connection status indicator**
- ✅ **Auto-reconnection**
- ✅ **Fallback to polling**

#### **Mobile App Features:**
- ✅ **Real-time message delivery**
- ✅ **Push notifications**
- ✅ **Message status updates**
- ✅ **Offline message queuing**
- ✅ **Auto-reconnection**

### **8. Security Features**

- ✅ **Authentication required** for all endpoints
- ✅ **User authorization** checks
- ✅ **Private channels** for message delivery
- ✅ **CSRF protection**
- ✅ **Rate limiting** (built into Laravel)
- ✅ **Input validation** and sanitization

### **9. Performance Optimizations**

- ✅ **Efficient database queries**
- ✅ **Message pagination**
- ✅ **Connection pooling**
- ✅ **Caching** for frequently accessed data
- ✅ **Background processing** for heavy operations

### **10. Monitoring and Logging**

- ✅ **Comprehensive error logging**
- ✅ **Message delivery tracking**
- ✅ **Connection monitoring**
- ✅ **Performance metrics**

## **🎯 How It Works**

### **Message Flow:**

1. **Mobile App** sends message via API
2. **Laravel** saves message to database
3. **Laravel** broadcasts message via WebSocket
4. **Website** receives message instantly
5. **Website** shows notification to user
6. **User** can reply immediately

### **Real-time Features:**

- **Instant delivery** (milliseconds)
- **Bidirectional communication**
- **Status updates** (read/unread)
- **Connection management**
- **Auto-reconnection**

## **🔧 Troubleshooting**

### **Common Issues:**

1. **WebSocket not connecting:**
   - Check Pusher credentials
   - Verify authentication token
   - Check network connectivity

2. **Messages not delivering:**
   - Check broadcasting configuration
   - Verify user permissions
   - Check database connection

3. **Notifications not showing:**
   - Check browser notification permissions
   - Verify JavaScript console for errors
   - Check authentication status

### **Debug Steps:**

1. **Check browser console** for JavaScript errors
2. **Verify API responses** in Network tab
3. **Test WebSocket connection** in Pusher dashboard
4. **Check Laravel logs** for server errors
5. **Verify database** message records

## **📱 Mobile App Integration Example**

```dart
// Complete Flutter implementation
class MessagingScreen extends StatefulWidget {
  @override
  _MessagingScreenState createState() => _MessagingScreenState();
}

class _MessagingScreenState extends State<MessagingScreen> {
  final MessagingService _messagingService = MessagingService();
  List<Message> messages = [];
  
  @override
  void initState() {
    super.initState();
    _initializeMessaging();
  }
  
  void _initializeMessaging() async {
    // Initialize WebSocket connection
    await _messagingService.initializePusher(authToken);
    
    // Load initial messages
    await _loadMessages();
  }
  
  void _loadMessages() async {
    final response = await _messagingService.getMessages();
    setState(() {
      messages = response;
    });
  }
  
  void _sendMessage(String text) async {
    await _messagingService.sendMessage(
      recipientId: selectedRecipientId,
      subject: 'New Message',
      message: text,
    );
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Messages')),
      body: ListView.builder(
        itemCount: messages.length,
        itemBuilder: (context, index) {
          final message = messages[index];
          return MessageTile(message: message);
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _showSendMessageDialog(),
        child: Icon(Icons.send),
      ),
    );
  }
}
```

## **✅ Implementation Complete!**

Your real-time messaging system is now ready with:

- ✅ **Complete API endpoints**
- ✅ **Real-time WebSocket communication**
- ✅ **Website integration**
- ✅ **Mobile app support**
- ✅ **Notification system**
- ✅ **Security features**
- ✅ **Performance optimizations**

**Test the system and enjoy real-time messaging between your website and mobile app!** 🎉
