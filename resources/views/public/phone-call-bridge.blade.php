<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Call Bansal Immigration</title>
</head>
<body style="margin:0;font-family:Arial,sans-serif;text-align:center;padding:2rem;background:#f4f4f4;color:#333;">
  <p style="font-size:16px;line-height:1.5;">Connecting your call to <strong>{{ $displayPhone }}</strong>…</p>
  <p style="margin-top:1.5rem;">
    <a id="dial" href="{{ $telUri }}" style="display:inline-block;padding:14px 28px;background:#1c2a3a;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;">Tap to call {{ $displayPhone }}</a>
  </p>
  <p style="margin-top:1rem;font-size:13px;color:#666;">If nothing happens, tap the button above.</p>
  <script>
    (function () {
      var u = @json($telUri);
      window.location.href = u;
    })();
  </script>
</body>
</html>
