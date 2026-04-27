<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lead/Client Information Form — Bansal Immigration</title>
    <style>
        :root { --b:#0f3d2e; --b2:#1a5c45; --fg:#1a1a1a; --muted:#5c5c5c; --bg:#f6f8f7; }
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; background: var(--bg); color: var(--fg); margin: 0; line-height: 1.5; }
        .wrap { max-width: 520px; margin: 0 auto; padding: 2rem 1.25rem 3rem; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); padding: 1.75rem 1.5rem; }
        h1 { font-size: 1.35rem; color: var(--b); margin: 0 0 .5rem; }
        p.lead { color: var(--muted); font-size: .95rem; margin: 0 0 1.25rem; }
        label { display: block; font-weight: 600; font-size: .8rem; margin: .75rem 0 .25rem; }
        .req { color: #b00020; }
        input, textarea { width: 100%; padding: .6rem .65rem; border: 1px solid #c8d4cf; border-radius: 6px; font-size: 1rem; }
        textarea { min-height: 88px; resize: vertical; }
        input:focus, textarea:focus { outline: 2px solid var(--b2); border-color: var(--b2); }
        .err { color: #b00020; font-size: .85rem; margin-top: .2rem; }
        .msg { padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .9rem; }
        .msg.ok { background: #e6f4ef; color: #0d4a2f; }
        .msg.err { background: #fdeaea; color: #7a1a1a; }
        .msg.info { background: #e8f0fe; color: #1a3a5c; }
        button { background: var(--b); color: #fff; border: 0; padding: .75rem 1.4rem; font-size: 1rem; font-weight: 600; border-radius: 8px; cursor: pointer; width: 100%; margin-top: 1.25rem; }
        button:hover { background: var(--b2); }
        .foot { text-align: center; font-size: .8rem; color: var(--muted); margin-top: 2rem; }
        .details-dl { margin: 0 0 1rem; }
        .details-dl dt { font-weight: 600; font-size: .8rem; color: var(--muted); margin-top: .5rem; }
        .details-dl dd { margin: .15rem 0 0 0; }
        .muted { color: var(--muted); font-size: .9rem; }
        .actions-row button { width: auto; margin-top: 0; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            @yield('content')
        </div>
        <p class="foot">Bansal Immigration</p>
    </div>
</body>
</html>
