<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Ad Has Been Deactivated</title>
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

        .email-body strong {
            font-weight: bold;
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
                <h1>Your Ad Has Been Deactivated</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                <p>Hello,</p>
                <p>Your ad titled "<strong>{{ $ad->title }}</strong>" has been deactivated due to zero clicks.</p>
                <p>If you have any questions, please contact support.</p>
                <a href="#" class="email-button">Contact Support</a>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p>If you have any questions, feel free to <a href="mailto:support@africtv.com">contact us</a>.</p>
                <p>&copy; {{ date('Y') }} AfricTv. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
