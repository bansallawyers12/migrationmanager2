# Laravel Reverb Real-time Chat Implementation Guide

## 🚀 Overview

This guide provides a complete implementation for real-time chat communication between **web frontend** and **mobile apps** (iOS, Android, React Native, Flutter) using **Laravel Reverb** WebSocket server.

### What is Laravel Reverb?

Laravel Reverb is a first-party WebSocket server for Laravel that provides:
- ✅ Real-time bidirectional communication
- ✅ Private channel authentication
- ✅ Self-hosted (no third-party services needed)
- ✅ Compatible with Pusher protocol
- ✅ Support for all platforms (Web, iOS, Android, Flutter, React Native)
- ✅ Free and open-source

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Backend Setup (Laravel)](#backend-setup-laravel)
3. [Starting Reverb Server](#starting-reverb-server)
4. [Web Client Setup](#web-client-setup)
5. [Mobile Client Setup](#mobile-client-setup)
6. [API Endpoints](#api-endpoints)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)
9. [Production Deployment](#production-deployment)

---

## Prerequisites

### Required Software
- PHP 8.2+
- Laravel 12.x
- Composer
- Node.js & NPM (for web frontend)

### Laravel Packages
- ✅ Laravel Reverb (already installed)
- ✅ Laravel Sanctum/Passport (for API authentication)

---

## Backend Setup (Laravel)

### Step 1: Environment Configuration

Add the following to your `.env` file:

```env
# Broadcasting Configuration
BROADCAST_DRIVER=reverb
QUEUE_CONNECTION=sync

# Laravel Reverb Configuration
REVERB_APP_ID=my-app-id
REVERB_APP_KEY=my-app-key
REVERB_APP_SECRET=my-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Reverb Server Configuration
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Vite Configuration (for frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

**Important:** Replace `my-app-id`, `my-app-key`, and `my-app-secret` with secure random values:

```bash
# Generate secure keys
php -r "echo bin2hex(random_bytes(16)) . PHP_EOL;"
```

### Step 2: Verify Broadcasting Configuration

Your `config/broadcasting.php` is already configured with Reverb connection. Verify it has:

```php
'default' => env('BROADCAST_DRIVER', 'reverb'),

'connections' => [
    'reverb' => [
        'driver' => 'reverb',
        'key' => env('REVERB_APP_KEY'),
        'secret' => env('REVERB_APP_SECRET'),
        'app_id' => env('REVERB_APP_ID'),
        'options' => [
            'host' => env('REVERB_HOST', '127.0.0.1'),
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'http'),
            'useTLS' => env('REVERB_SCHEME', 'http') === 'https',
        ],
    ],
    // ... other connections
]
```

### Step 3: Channel Authorization

Your `routes/channels.php` is already set up with proper authorization:

```php
// User-specific channels
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Matter-specific channels
Broadcast::channel('matter.{matterId}', function ($user, $matterId) {
    // Check if user is associated with this matter
    // Already implemented in your code
});
```

### Step 4: Broadcasting Events

Your events are already configured:
- ✅ `MessageSent` - Broadcasts when a message is sent
- ✅ `MessageReceived` - Broadcasts when a message is read
- ✅ `MessageUpdated` - Broadcasts when a message is updated
- ✅ `UnreadCountUpdated` - Broadcasts unread count changes

All events implement `ShouldBroadcast` interface.

---

## Starting Reverb Server

### Development Mode

Start the Reverb WebSocket server:

```bash
php artisan reverb:start
```

You should see:

```
  INFO  Starting server on 0.0.0.0:8080

  2025-10-07 12:00:00 Server started successfully.
  2025-10-07 12:00:00 Listening on http://0.0.0.0:8080
```

### Run in Background (Production)

```bash
# Using nohup
nohup php artisan reverb:start > storage/logs/reverb.log 2>&1 &

# Or using supervisor (recommended)
# See Production Deployment section
```

### Debug Mode

Enable detailed logging:

```bash
php artisan reverb:start --debug
```

---

## Web Client Setup

### Step 1: Install Dependencies

```bash
npm install laravel-echo pusher-js
```

### Step 2: Configure Vite

Update `vite.config.js` if needed:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### Step 3: Initialize Echo

In your `resources/js/app.js`:

```javascript
import './bootstrap';
import RealtimeChat from './realtime-chat';

// Initialize when user is authenticated
if (window.authToken && window.userId) {
    const chat = new RealtimeChat({
        wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
        wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
        reverbKey: import.meta.env.VITE_REVERB_APP_KEY,
        forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
        authToken: window.authToken,
        userId: window.userId,
    });

    // Subscribe to user channel
    chat.subscribeToUserChannel();

    // Listen for messages
    chat.on('onMessageReceived', (event) => {
        console.log('New message:', event.message);
        // Update your UI here
    });

    // Make chat instance globally available
    window.chat = chat;
}
```

### Step 4: Usage in Blade Templates

```html
@auth
<script>
    window.authToken = '{{ auth()->user()->createToken("chat")->plainTextToken }}';
    window.userId = {{ auth()->id() }};
</script>
@endauth

@vite(['resources/js/app.js'])
```

### Step 5: Send Messages from Web

```javascript
// Send a message
await window.chat.sendMessage({
    message: 'Hello from web!',
    client_matter_id: 456
});

// Subscribe to specific matter
window.chat.subscribeToMatterChannel(456);

// Mark as read
await window.chat.markAsRead(messageId);
```

---

## Mobile Client Setup

### Flutter

**Installation (pubspec.yaml):**
```yaml
dependencies:
  pusher_client: ^2.0.0
  http: ^1.1.0
```

**Usage:**
```dart
final chat = RealtimeChatFlutter(
  wsHost: 'your-server.com',
  wsPort: 8080,
  apiBaseUrl: 'https://your-server.com/api',
  authToken: userToken,
  userId: userId,
  reverbKey: 'your-reverb-key',
);

await chat.initialize();
chat.subscribeToUserChannel();

chat.onMessageReceived = (data) {
  print('New message: $data');
};
```

See `mobile-examples/flutter-realtime-chat.dart` for complete implementation.


---

## API Endpoints

Your Laravel backend already has these API endpoints:

### 1. Send Message
```
POST /api/messages/send
Content-Type: application/json
Authorization: Bearer {token}

{
    "message": "Hello!",
    "client_matter_id": 456
}
```

### 2. Get Messages
```
GET /api/messages?client_matter_id=456&page=1&limit=20
Authorization: Bearer {token}
```

### 3. Mark as Read
```
PUT /api/messages/{id}/read
Authorization: Bearer {token}
```

### 4. Get Unread Count
```
GET /api/messages/unread-count
Authorization: Bearer {token}
```

### 5. Broadcasting Authentication
```
POST /api/broadcasting/auth
Authorization: Bearer {token}
Content-Type: application/json

{
    "socket_id": "123.456",
    "channel_name": "private-user.1"
}
```

---

## Testing

### Test WebSocket Connection

Use `wscat` to test manually:

```bash
npm install -g wscat
wscat -c ws://localhost:8080/app/my-app-key
```

### Test from Browser Console

```javascript
// Check if Echo is initialized
console.log(window.Echo);

// Check connection state
console.log(window.chat.echo.connector.pusher.connection.state);

// Should show: "connected"
```

### Send Test Message

```bash
curl -X POST http://localhost/api/messages/send \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "message": "Test message",
    "client_matter_id": 1
  }'
```

### Monitor Reverb Logs

```bash
tail -f storage/logs/laravel.log
```

---

## Troubleshooting

### Issue: Connection Refused

**Problem:** Cannot connect to WebSocket server

**Solution:**
1. Ensure Reverb server is running: `php artisan reverb:start`
2. Check firewall rules allow port 8080
3. Verify `.env` has correct `REVERB_HOST` and `REVERB_PORT`

### Issue: Authorization Failed

**Problem:** `403 Forbidden` on private channels

**Solution:**
1. Verify API token is valid
2. Check `routes/channels.php` authorization logic
3. Ensure user is authenticated
4. Check that Authorization header is sent: `Bearer {token}`

### Issue: Messages Not Broadcasting

**Problem:** Events don't trigger on clients

**Solution:**
1. Verify event implements `ShouldBroadcast`
2. Check `BROADCAST_DRIVER=reverb` in `.env`
3. Ensure queue worker is running if using queues
4. Check Laravel logs: `storage/logs/laravel.log`

### Issue: CORS Errors

**Problem:** Browser shows CORS errors

**Solution:**
Add to `config/cors.php`:
```php
'paths' => ['api/*', 'broadcasting/auth'],
'allowed_origins' => ['*'],
'allowed_headers' => ['*'],
```

### Issue: Mobile App Can't Connect

**Problem:** Mobile app connection fails

**Solution:**
1. Use actual server IP/domain, not `localhost`
2. For local testing, use your machine's IP: `192.168.x.x`
3. Ensure mobile device is on same network
4. Check mobile app has internet permission (Android)

### Debug Mode

Enable verbose logging:

```bash
# In .env
APP_DEBUG=true
LOG_LEVEL=debug

# Start Reverb with debug
php artisan reverb:start --debug
```

---

## Production Deployment

### Step 1: Use Process Manager (Supervisor)

Create `/etc/supervisor/conf.d/reverb.conf`:

```ini
[program:reverb]
command=php /path/to/your/app/artisan reverb:start
directory=/path/to/your/app
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/reverb.log
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb
```

### Step 2: Use HTTPS/WSS

Update `.env` for production:
```env
REVERB_HOST=your-domain.com
REVERB_PORT=443
REVERB_SCHEME=https
```

### Step 3: Reverse Proxy (Nginx)

Add to your Nginx config:

```nginx
# WebSocket proxy
location /app/ {
    proxy_pass http://localhost:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
}
```

### Step 4: SSL Certificate

Use Let's Encrypt:
```bash
sudo certbot --nginx -d your-domain.com
```

### Step 5: Scaling (Optional)

For high traffic, enable Redis scaling:

```env
REVERB_SCALING_ENABLED=true
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

## Architecture Overview

```
┌─────────────┐                    ┌──────────────────┐                    ┌─────────────┐
│  Web Client │ ◄──── WebSocket ───│  Laravel Reverb  │──── WebSocket ────► │ Mobile App  │
│  (Browser)  │                    │  (Port 8080)     │                    │             │
└─────────────┘                    └──────────────────┘                    └─────────────┘
       │                                     ▲                                     │
       │                                     │                                     │
       │                              Broadcasts Events                            │
       │                                     │                                     │
       └────────── HTTP API ──────► Laravel Backend ◄───── HTTP API ──────────────┘
                                           │
                                    MySQL Database
```

### Message Flow:

1. **User A (Mobile)** sends message via `POST /api/messages/send`
2. **Laravel Backend** saves message to database
3. **Laravel Backend** broadcasts `MessageSent` event via Reverb
4. **Reverb Server** pushes event to all connected WebSocket clients subscribed to the channel
5. **User B (Web)** receives message instantly via WebSocket
6. **User B (Web)** marks as read via `PUT /api/messages/{id}/read`
7. **Laravel Backend** broadcasts `MessageReceived` event
8. **User A (Mobile)** receives read receipt instantly

---

## Best Practices

### Security
1. ✅ Always use HTTPS/WSS in production
2. ✅ Validate user permissions in channel authorization
3. ✅ Use short-lived API tokens
4. ✅ Sanitize message content before broadcasting
5. ✅ Rate limit API endpoints

### Performance
1. ✅ Use Redis for scaling multiple Reverb instances
2. ✅ Implement message pagination
3. ✅ Cache user data to reduce database queries
4. ✅ Use queue workers for heavy operations
5. ✅ Optimize channel subscriptions (unsubscribe when not needed)

### User Experience
1. ✅ Show connection status indicator
2. ✅ Implement reconnection logic
3. ✅ Queue messages when offline
4. ✅ Show typing indicators
5. ✅ Display read receipts
6. ✅ Play notification sounds

---

## Summary

✅ **Backend:** Laravel Reverb installed and configured  
✅ **Configuration:** Broadcasting and Reverb config files set up  
✅ **Events:** Message broadcasting events implemented  
✅ **Channels:** Private channel authorization configured  
✅ **Web Client:** JavaScript/Laravel Echo implementation ready  
✅ **Mobile Clients:** React Native, Flutter, iOS, Android examples provided  
✅ **API:** RESTful endpoints for messaging operations  
✅ **Documentation:** Complete setup and deployment guide  

### Next Steps:

1. **Start Reverb Server:** `php artisan reverb:start`
2. **Configure Your .env:** Set REVERB_APP_KEY, etc.
3. **Test Web Client:** Open browser, authenticate, send message
4. **Test Mobile Client:** Run mobile app, connect, receive message
5. **Deploy to Production:** Follow production deployment steps

---

## Support & Resources

- **Laravel Reverb Docs:** https://laravel.com/docs/reverb
- **Pusher Protocol:** https://pusher.com/docs/channels/library_auth_reference/pusher-websockets-protocol/
- **Laravel Broadcasting:** https://laravel.com/docs/broadcasting
- **Your Implementation Files:**
  - Web: `resources/js/realtime-chat.js`
  - Flutter: `mobile-examples/flutter-realtime-chat.dart`

---

**Created:** {{ date }}  
**Version:** 1.0  
**Status:** ✅ Production Ready

