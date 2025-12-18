<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px 20px;
        }
        .email-container p {
            color: #1a1a1a;
            font-size: 15px;
            line-height: 1.6;
        }
        .email-container h1,
        .email-container h2,
        .email-container h3,
        .email-container h4 {
            color: #1a1a1a;
        }
        .email-container a {
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        {!! $content !!}
    </div>
</body>
</html>
