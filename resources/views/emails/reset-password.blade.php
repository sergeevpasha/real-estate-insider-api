<!DOCTYPE html>
<html>
<head>
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #ffffff;
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }

        .header {
            background-color: #f8f8f8;
            padding: 10px;
            text-align: center;
        }

        .content {
            padding: 20px;
            text-align: center;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <!-- Your Logo Here -->
        <img src="data:image/png;base64,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"
             alt="Real Estate">
    </div>
    <div class="content">
        <h2>Reset Your Password</h2>
        <p>
            Hello!
            You are receiving this email because we received a password reset request for your account.
        </p>
        <a href="{{ $url }}" class="button">Reset Your Password</a>
        <div style="font-size: 0.85em;">This password reset link will expire in 60 minutes.</div>
        <p>
            If you did not request a password reset, no further action is required.
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Your Company. All rights reserved.
    </div>
</div>
</body>
</html>
