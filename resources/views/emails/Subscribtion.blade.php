<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Subscriber Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 10px 0;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .content {
            text-align: center;
            padding: 20px 0;
        }
        .content p {
            margin: 10px 0;
            color: #666;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 5px;
            text-decoration: none;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #aaa;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Congratulations!</h1>
        </div>
        <div class="content">
            <p>You have a new subscriber.</p>
            <p>Subscriber Email: {{ $subscribtion->subscriber_email }}</p>
            <a href="{{ url('/') }}" class="button">Login</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} AfricTv. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
