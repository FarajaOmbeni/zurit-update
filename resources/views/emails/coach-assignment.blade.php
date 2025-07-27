<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Assignment</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: #667eea;
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
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>🎯 Welcome to Your Coaching Journey!</h1>
        <p>You've been assigned a personal coach to help you achieve your financial goals</p>
    </div>

    <div class="content">
        <h2>Hello {{ $user->name }},</h2>

        <p>Great news! You've been assigned a personal coach to support you on your financial journey. Your coach is
            here to help you:</p>

        <ul>
            <li>Set and achieve your financial goals</li>
            <li>Create personalized investment strategies</li>
            <li>Develop better money management habits</li>
            <li>Navigate financial challenges and opportunities</li>
        </ul>

        <div class="coach-card">
            <h3>Meet Your Coach</h3>

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

            @if ($coach->bio)
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 15px;">
                    <p style="margin: 0; font-style: italic;">"{{ $coach->bio }}"</p>
                </div>
            @endif
        </div>

        <div class="highlight">
            <strong>What's Next?</strong>
            <p style="margin: 10px 0 0 0;">Your coach will reach out to you soon to schedule your first session. In the
                meantime, you can:</p>
            <ul style="margin: 10px 0 0 0;">
                <li>Log into your dashboard to explore your financial tools</li>
                <li>Set up your financial goals and track your progress</li>
                <li>Prepare any questions you'd like to discuss with your coach</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('home') }}" class="btn">Access Your Dashboard</a>
        </div>

        <p style="margin-top: 30px;">
            If you have any questions about your coaching assignment, please don't hesitate to contact us at
            <a href="mailto:support@zuritconsulting.com">support@zuritconsulting.com</a>
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
