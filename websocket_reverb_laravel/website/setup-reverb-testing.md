# 🧪 How to Test Laravel Reverb - Complete Guide

## Issue: "Application does not exist" Error

Based on your test page, you're getting error code 4001. This means the App Key doesn't match your Reverb configuration.

## Step 1: Configure Your Environment

Create or update your `.env` file with these settings:

```env
# Broadcasting Configuration
BROADCAST_DRIVER=reverb

# Generate these keys (run the commands below):
REVERB_APP_ID=my-app-id
REVERB_APP_KEY=my-app-key  
REVERB_APP_SECRET=my-app-secret

# Connection Settings
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

# Frontend Settings
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## Step 2: Generate Secure Keys

Run these commands to generate secure keys:

```bash
# Generate App ID
php -r "echo 'REVERB_APP_ID=' . bin2hex(random_bytes(8)) . PHP_EOL;"

# Generate App Key  
php -r "echo 'REVERB_APP_KEY=' . bin2hex(random_bytes(16)) . PHP_EOL;"

# Generate App Secret
php -r "echo 'REVERB_APP_SECRET=' . bin2hex(random_bytes(32)) . PHP_EOL;"
```

Copy the output and add it to your `.env` file.

## Step 3: Clear Configuration Cache

```bash
php artisan config:clear
php artisan config:cache
```

## Step 4: Start Reverb Server

```bash
php artisan reverb:start
```

You should see:
```
INFO  Starting server on 0.0.0.0:8080
Server started successfully.
```

## Step 5: Test WebSocket Connection

1. **Open the test page:** `http://localhost:8000/test-websocket-connection.html`
2. **Update the App Key** in the test page to match your `REVERB_APP_KEY` from `.env`
3. **Click Connect**

## Step 6: Test API Endpoints

Run the API test script:

```bash
php test-api-endpoints.php
```

## Step 7: Test with Authentication

If you have a user token, test with authentication:

```bash
php test-api-endpoints.php http://localhost:8000 your-bearer-token 1 1
```

## Troubleshooting

### Error: "Application does not exist"
- ✅ Check `REVERB_APP_KEY` in `.env` matches test page
- ✅ Run `php artisan config:clear && php artisan config:cache`
- ✅ Restart Reverb server: `php artisan reverb:start`

### Error: "Connection refused"
- ✅ Check Reverb server is running: `php artisan reverb:start`
- ✅ Check port 8080 is not blocked by firewall
- ✅ Verify `REVERB_SERVER_PORT=8080` in `.env`

### Error: "Authorization failed"
- ✅ Check API token is valid
- ✅ Verify user exists and is authenticated
- ✅ Check `routes/channels.php` authorization logic

## Testing Checklist

- [ ] Reverb server running (`php artisan reverb:start`)
- [ ] `.env` configured with correct keys
- [ ] Configuration cached (`php artisan config:cache`)
- [ ] WebSocket test page connects successfully
- [ ] API endpoints respond correctly
- [ ] Messages broadcast in real-time
- [ ] Mobile app can connect (use your computer's IP)

## Quick Test Commands

```bash
# 1. Check if Reverb is running
curl -I http://localhost:8080

# 2. Test Laravel app
curl -I http://localhost:8000

# 3. Send test message (if authenticated)
curl -X POST http://localhost:8000/api/messages/send \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"message": "Test", "client_matter_id": 1}'

# 4. Check Laravel logs
tail -f storage/logs/laravel.log
```

## Mobile Testing

For mobile apps, replace `localhost` with your computer's IP:

```bash
# Find your IP address
# Windows:
ipconfig

# Mac/Linux:
ifconfig

# Then use: http://192.168.1.100:8000 (replace with your IP)
```

## Production Testing

For production, use HTTPS/WSS:

```env
REVERB_HOST=your-domain.com
REVERB_PORT=443
REVERB_SCHEME=https
```

## Files Created for Testing

- ✅ `test-websocket-connection.html` - WebSocket connection tester
- ✅ `test-api-endpoints.php` - API endpoint tester
- ✅ `setup-reverb-testing.md` - This guide

## Next Steps

1. ✅ Fix the App Key configuration
2. ✅ Test WebSocket connection
3. ✅ Test API endpoints  
4. ✅ Test real-time messaging
5. ✅ Deploy to production

## Need Help?

Check these files:
- `QUICK_START_REVERB.md` - Quick reference
- `LARAVEL_REVERB_REALTIME_CHAT_GUIDE.md` - Complete documentation
- `reverb.env.example` - Environment configuration template
