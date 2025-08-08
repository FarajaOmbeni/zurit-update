<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Deassignment</title>
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
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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

        .coach-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #dc3545;
        }

        .coach-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 15px;
            display: block;
        }

        .btn {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 12px 30px;
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

        .highlight {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #721c24;
        }

        .info-box {
            background: #e2e3e5;
            border: 1px solid #d6d8db;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>👋 Coach Assignment Ended</h1>
        <p>Your coaching relationship has been concluded</p>
    </div>

    <div class="content">
        <h2>Hello {{ $user->name }},</h2>

        <p>We wanted to inform you that your coaching relationship with <strong>{{ $coach->name }}</strong> has been
            ended.</p>

        <div class="coach-card">
            <h3>Your Previous Coach</h3>

            @if ($coach->photo)
                <img src="{{ asset($coach->photo) }}" alt="{{ $coach->name }}" class="coach-photo">
            @else
                <div class="coach-photo"
                    style="background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                    <span style="font-size: 24px;">👤</span>
                </div>
            @endif

            <h4 style="text-align: center; margin: 0 0 10px 0;">{{ $coach->name }}</h4>
            <p style="text-align: center; margin: 0 0 15px 0; color: #666;">{{ $coach->email }}</p>

            @if ($coach->phone)
                <p style="text-align: center; margin: 0 0 15px 0; color: #666;">
                    📞 {{ $coach->phone }}
                </p>
            @endif
        </div>

        <div class="highlight">
            <strong>What This Means:</strong>
            <ul style="margin: 10px 0 0 0;">
                <li>You no longer have an assigned coach</li>
                <li>Your financial tools and progress remain accessible</li>
                <li>You can request a new coach assignment anytime</li>
                <li>Your financial goals and investments are still active</li>
            </ul>
        </div>

        <div class="info-box">
            <h4 style="margin: 0 0 10px 0;">💡 Next Steps:</h4>
            <ul style="margin: 0;">
                <li>Continue using your financial planning tools</li>
                <li>Track your progress toward your goals</li>
                <li>Consider requesting a new coach if needed</li>
                <li>Reach out to our support team for assistance</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('home') }}" class="btn">Access Your Dashboard</a>
        </div>

        <p style="margin-top: 30px;">
            If you have any questions about this change or would like to be assigned a new coach,
            please contact us at <a href="mailto:support@zuritconsulting.com">support@zuritconsulting.com</a>
        </p>

        <p>Best regards,<br>
            <strong>The Zurit Team</strong>
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Zurit Consulting. All rights reserved.</p>
        <p>This email was sent to {{ $user->email }}</p>
    </div>
</body>

</html>
