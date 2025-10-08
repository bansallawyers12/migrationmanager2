# Frontend Dependencies for Laravel Reverb

## NPM Packages (Web)

Add these to your `package.json`:

```json
{
  "dependencies": {
    "laravel-echo": "^1.15.3",
    "pusher-js": "^8.4.0-rc2"
  }
}
```

**Install:**
```bash
npm install laravel-echo pusher-js
```

---

## Mobile App Dependencies


### Flutter

**pubspec.yaml:**
```yaml
dependencies:
  flutter:
    sdk: flutter
  pusher_client: ^2.0.0
  http: ^1.1.0
```

**Install:**
```bash
flutter pub get
```


---

## Environment Variables

### Web (.env)
```env
VITE_REVERB_APP_KEY=your-app-key
VITE_REVERB_HOST=localhost
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http
```

### Mobile (Config File)

Create `config.js` or equivalent:

```javascript
export const REVERB_CONFIG = {
  wsHost: 'your-server.com',
  wsPort: 8080,
  apiBaseUrl: 'https://your-server.com/api',
  reverbKey: 'your-reverb-app-key',
};
```

---

## Permissions

### Android (AndroidManifest.xml)
```xml
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />
```

### iOS (Info.plist)
For local development (HTTP):
```xml
<key>NSAppTransportSecurity</key>
<dict>
    <key>NSAllowsArbitraryLoads</key>
    <true/>
</dict>
```

**Note:** Remove this in production and use HTTPS/WSS only.

---

## Build Commands

### Web
```bash
npm run dev          # Development
npm run build        # Production
```


### Flutter
```bash
flutter run
flutter build apk    # Android
flutter build ios    # iOS
```

---

## Verification

After installation, verify dependencies:

### Web
```bash
npm list laravel-echo pusher-js
```

### Flutter
```bash
flutter pub deps
```

---

## Common Issues

### Issue: Module not found

**Web:**
```bash
rm -rf node_modules package-lock.json
npm install
```

### Issue: Version conflicts

Use exact versions:
```json
{
  "dependencies": {
    "laravel-echo": "1.15.3",
    "pusher-js": "8.4.0-rc2"
  }
}
```

---

## Next Steps

1. ✅ Install dependencies for your platform
2. ✅ Configure environment variables
3. ✅ Import and use the chat client class
4. ✅ Test with Reverb server running
5. ✅ Deploy to production with HTTPS/WSS

See `QUICK_START_REVERB.md` for implementation examples.

