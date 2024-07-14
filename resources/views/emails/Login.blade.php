<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfricTv Login</title>
</head>
<body>
    <h2>Dear {{ $user->name }},</h2>
    <p>We noticed that you logged in!</p>
    <p>Device information:</p>
    <ul>
        <li>Browser: {{ request()->header('User-Agent') }}</li>
    </ul>
    <p>If this was you, you can ignore this message. Otherwise, please take appropriate action.</p>
    <a href="#">Login and Kindly message our help center</a>

</body>
</html>
