<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Assignment Notification</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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

        .assignment-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #28a745;
        }

        .user-info,
        .coach-info {
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
        }

        .info-details h4 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .info-details p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            background: #28a745;
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
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #155724;
        }

        .timestamp {
            background: #e2e3e5;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            font-size: 14px;
            color: #495057;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>✅ Coach Assignment Successful</h1>
        <p>A new client has been assigned to a coach</p>
    </div>

    <div class="content">
        <h2>Hello {{ $adminName }},</h2>

        <p>A new coach assignment has been successfully completed. Here are the details:</p>

        <div class="assignment-card">
            <h3>Assignment Details</h3>

            <div class="user-info">
                <div class="avatar">👤</div>
                <div class="info-details">
                    <h4>{{ $user->name }}</h4>
                    <p>{{ $user->email }}</p>
                    <p>Client ID: {{ $user->id }}</p>
                </div>
            </div>

            <div style="text-align: center; margin: 20px 0; font-size: 24px;">⬇️</div>

            <div class="coach-info">
                <div class="avatar">🎯</div>
                <div class="info-details">
                    <h4>{{ $coach->name }}</h4>
                    <p>{{ $coach->email }}</p>
                    <p>Coach ID: {{ $coach->id }}</p>
                    @if ($coach->phone)
                        <p>📞 {{ $coach->phone }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="highlight">
            <strong>✅ Assignment Confirmed</strong>
            <p style="margin: 10px 0 0 0;">
                The client has been successfully assigned to the coach.
                An email notification has been sent to the client with their coach's details.
            </p>
        </div>

        <div class="timestamp">
            <strong>Assignment Time:</strong> {{ now()->format('F j, Y \a\t g:i A') }}
        </div>

        <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #856404;">📊 Quick Stats</h4>
            <ul style="margin: 0; color: #856404;">
                <li>Total clients assigned to {{ $coach->name }}: {{ $coach->users()->count() }}</li>
                <li>Client joined: {{ $user->created_at->format('F j, Y') }}</li>
                <li>Assignment completed: {{ now()->format('F j, Y') }}</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('coaching.show', $coach->id) }}" class="btn">View Coach Details</a>
        </div>

        <p style="margin-top: 30px;">
            This is an automated notification. If you have any questions about this assignment,
            please check the admin dashboard or contact the development team.
        </p>

        <p>Best regards,<br>
            <strong>Zurit Admin System</strong>
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Zurit Consulting. All rights reserved.</p>
        <p>This is an automated system notification.</p>
    </div>
</body>

</html>
