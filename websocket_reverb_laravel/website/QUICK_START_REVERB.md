# 🚀 Quick Start - Laravel Reverb Real-time Chat

## Step 1: Configure Environment

Add to your `.env` file:

```env
BROADCAST_DRIVER=reverb

REVERB_APP_ID=my-app-id
REVERB_APP_KEY=my-app-key
REVERB_APP_SECRET=my-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

**Generate secure keys:**
```bash
php -r "echo 'REVERB_APP_ID=' . bin2hex(random_bytes(8)) . PHP_EOL;"
php -r "echo 'REVERB_APP_KEY=' . bin2hex(random_bytes(16)) . PHP_EOL;"
php -r "echo 'REVERB_APP_SECRET=' . bin2hex(random_bytes(32)) . PHP_EOL;"
```

## Step 2: Start Reverb Server

```bash
php artisan reverb:start
```

You should see:
```
INFO  Starting server on 0.0.0.0:8080
Server started successfully.
```

## Step 3: Test from Web

### Install Dependencies (if not already done)
```bash
npm install laravel-echo pusher-js
```

### Use in JavaScript
```javascript
import RealtimeChat from './resources/js/realtime-chat';

const chat = new RealtimeChat({
    wsHost: 'localhost',
    wsPort: 8080,
    reverbKey: 'your-app-key',
    authToken: 'user-bearer-token',
    userId: 123
});

// Subscribe to user channel
chat.subscribeToUserChannel();

// Listen for messages
chat.on('onMessageReceived', (event) => {
    console.log('New message:', event.message);
});

// Send a message
await chat.sendMessage({
    message: 'Hello!',
    client_matter_id: 456
});
```

## Step 4: Test from Mobile

### Flutter
```dart
final chat = RealtimeChatFlutter(
  wsHost: '192.168.1.100',
  wsPort: 8080,
  apiBaseUrl: 'http://192.168.1.100/api',
  authToken: userToken,
  userId: userId,
  reverbKey: 'your-app-key'
);

await chat.initialize();
chat.subscribeToUserChannel();
```

## Step 5: Verify It Works

### Terminal 1 - Start Reverb:
```bash
php artisan reverb:start --debug
```

### Terminal 2 - Send Test Message:
```bash
curl -X POST http://localhost/api/messages/send \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "message": "Test from API",
    "client_matter_id": 1
  }'
```

### Check Browser Console:
You should see: `📨 New message received: ...`

---

## 📁 Implementation Files

- **Web Client:** `resources/js/realtime-chat.js`
- **Flutter:** `mobile-examples/flutter-realtime-chat.dart`
- **Full Guide:** `LARAVEL_REVERB_REALTIME_CHAT_GUIDE.md`

---

## 🔧 Troubleshooting

**Can't connect to WebSocket?**
- ✅ Check Reverb is running: `php artisan reverb:start`
- ✅ Check firewall allows port 8080
- ✅ For mobile, use actual IP, not `localhost`

**Authorization failed?**
- ✅ Verify API token is valid
- ✅ Check `routes/channels.php` authorization
- ✅ Ensure `Authorization: Bearer {token}` header is sent

**Messages not broadcasting?**
- ✅ Verify `BROADCAST_DRIVER=reverb` in `.env`
- ✅ Check event implements `ShouldBroadcast`
- ✅ Check Laravel logs: `tail -f storage/logs/laravel.log`

---

## 🎯 What You Get

✅ **Real-time bidirectional chat** between web and mobile  
✅ **Private channels** with authentication  
✅ **Message read receipts**  
✅ **Unread count updates**  
✅ **Automatic reconnection**  
✅ **Production-ready code**  

**All platforms supported:**
- Web (JavaScript/Laravel Echo)
- Flutter

---

**Need help?** Check `LARAVEL_REVERB_REALTIME_CHAT_GUIDE.md` for detailed documentation.

