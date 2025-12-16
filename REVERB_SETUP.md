# Laravel Reverb Setup Guide

## Current Status

The application is configured to work **without** Laravel Reverb (WebSocket server). It uses a **polling fallback** that checks for notifications every 60 seconds.

## What Changed

The warnings about Laravel Echo have been suppressed when Reverb is intentionally not configured. The system will:
- ✅ Work perfectly with polling fallback (current setup)
- ✅ Automatically use WebSocket if Reverb is configured
- ✅ No console warnings when Reverb is not configured

## To Enable Real-Time WebSocket Notifications (Optional)

If you want real-time notifications instead of polling, follow these steps:

### 1. Install Reverb (if not already installed)

```bash
composer require laravel/reverb
php artisan reverb:install
```

### 2. Generate Reverb App Credentials

```bash
php artisan reverb:install
```

This will generate:
- `REVERB_APP_ID`
- `REVERB_APP_KEY`
- `REVERB_APP_SECRET`

### 3. Add to `.env` file

```env
# Reverb Configuration
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite needs these (prefixed with VITE_)
VITE_REVERB_APP_KEY=your-app-key
VITE_REVERB_HOST=127.0.0.1
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

### 4. Start Reverb Server

**Development:**
```bash
php artisan reverb:start
```

**Production (with Supervisor):**
Create `/etc/supervisor/conf.d/reverb.conf`:
```ini
[program:reverb]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/reverb.log
```

### 5. Rebuild Frontend Assets

After adding Vite environment variables, rebuild:

```bash
npm run build
# Or for development:
npm run dev
```

### 6. Verify

- Check browser console for: `✅ Laravel Echo initialized with Reverb`
- No warnings about Echo not being available
- Notifications appear instantly (not after 60 seconds)

## Current Behavior (Without Reverb)

- ✅ **Notifications work** via HTTP polling every 60 seconds
- ✅ **No console warnings** (warnings suppressed)
- ✅ **All functionality intact**
- ⚠️ **Slight delay** (up to 60 seconds) for new notifications

## Troubleshooting

### Warnings Still Appear

1. Clear browser cache
2. Rebuild assets: `npm run build`
3. Check that `VITE_REVERB_APP_KEY` is not set in `.env` (if you want polling)

### Reverb Not Connecting

1. Ensure Reverb server is running: `php artisan reverb:start`
2. Check firewall allows port 8080
3. Verify environment variables are correct
4. Check `storage/logs/reverb.log` for errors

### Notifications Not Working

1. Check browser console for errors
2. Verify `broadcasts.js` is loaded
3. Check network tab for `/notifications/broadcasts/unread` requests
4. Verify user is authenticated

## Notes

- The system gracefully falls back to polling if Reverb is unavailable
- No breaking changes - existing functionality preserved
- Warnings are suppressed when Reverb is intentionally not configured
- Real-time features are optional and can be enabled later

