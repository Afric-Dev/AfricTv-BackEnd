<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfricTv Login</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f8fa;
            color: #333;
        }

        .email-wrapper {
            width: 100%;
            padding: 20px 0;
            background-color: #f5f8fa;
        }

        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Header */
        .email-header {
            background-color: #008000; /* Green */
            padding: 20px;
            text-align: center;
            color: #ffffff;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        /* Body */
        .email-body {
            padding: 20px;
        }

        .email-body p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .email-body ul {
            padding-left: 20px;
            margin: 10px 0;
            list-style: disc;
        }

        .email-body li {
            font-size: 16px;
            line-height: 1.5;
        }

        .email-button {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 20px;
            color: #ffffff;
            background-color: #008000; /* Green */
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            text-align: center;
        }

        .email-button:hover {
            background-color: #006400; /* Darker Green */
        }

        /* Footer */
        .email-footer {
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #777;
            background-color: #f9f9f9;
        }

        .email-footer a {
            color: #008000;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <!-- Header -->
            <div class="email-header">
                <h1>AfricTv Login Alert</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                <p>Dear {{ $user->name }},</p>
                <p>We noticed that you logged in!</p>
                <p>Device Information:</p>
                <ul>
                    <li>Browser: {{ request()->header('User-Agent') }}</li>
                </ul>
                <p>If this was you, you can ignore this message. Otherwise, please take appropriate action.</p>
                <a href="www.africtv.com.ng/login" class="email-button">Login and Message Our Help Center</a>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>If you have any questions, feel free to <a href="mailto:customersupport@africtv.com.ng">contact us</a>.</p>
                <p>&copy; {{ date('Y') }} AfricTv. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
