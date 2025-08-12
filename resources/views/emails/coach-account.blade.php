<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Coach Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #6f42c1 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }

        .info-card {
            background: white;
            border-left: 4px solid #6f42c1;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .btn {
            display: inline-block;
            background: #6f42c1;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="header">
        @if (!isset($isExisting) || !$isExisting)
            <h1>Welcome to the Coach Portal!</h1>
            <p>Your account has been created successfully.</p>
        @else
            <h1>Welcome to the Coach Portal!</h1>
            <p>Your coach access has been enabled.</p>
        @endif
    </div>

    <div class="content">
        <h2>Hello {{ $user->name }},</h2>

        @if (!isset($isExisting) || !$isExisting)
            <p>We're excited to welcome you as a coach! You can now log in to your account using the credentials below:
            </p>

            <div class="info-card">
                <p><strong>Email: {{ $user->email }}</strong></p>
                <p><strong>Temporary Password: {{ $password }}</strong></p>
            </div>

            <p style="margin-top: 20px;">
                For your security, please visit this <strong><a href="{{ route('password.request') }}"
                        class="btn">link</a></strong>.
            </p>
        @else
            <p>Welcome aboard as a coach! Your account already exists with Zurit Consulting. Please use your existing
                credentials to log in.</p>
            <div class="info-card">
                <p><strong>Email: {{ $user->email }}</strong></p>
            </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('login') }}" class="btn">Log In Now</a>
        </div>

        <p>If you have any questions or need help, feel free to contact the Zurit admin team.</p>

        <p>Best regards,<br>
            <strong>Zurit Admin System</strong>
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Zurit Consulting. All rights reserved.</p>
        <p>This is an automated message. Please do not reply.</p>
    </div>

</body>

</html>
