<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Deassignment Notification</title>
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

        .deassignment-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #dc3545;
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

        .timestamp {
            background: #e2e3e5;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            font-size: 14px;
            color: #495057;
        }

        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>❌ Coach Deassignment Completed</h1>
        <p>A client has been removed from their coach</p>
    </div>

    <div class="content">
        <h2>Hello {{ $adminName }},</h2>

        <p>A coach deassignment has been completed. Here are the details:</p>

        <div class="deassignment-card">
            <h3>Deassignment Details</h3>

            <div class="user-info">
                <div class="avatar">👤</div>
                <div class="info-details">
                    <h4>{{ $user->name }}</h4>
                    <p>{{ $user->email }}</p>
                    <p>Client ID: {{ $user->id }}</p>
                </div>
            </div>

            <div style="text-align: center; margin: 20px 0; font-size: 24px;">❌</div>

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
            <strong>❌ Deassignment Confirmed</strong>
            <p style="margin: 10px 0 0 0;">
                The client has been successfully removed from the coach.
                An email notification has been sent to the client informing them of this change.
            </p>
        </div>

        <div class="timestamp">
            <strong>Deassignment Time:</strong> {{ now()->format('F j, Y \a\t g:i A') }}
        </div>

        <div class="warning-box">
            <h4 style="margin: 0 0 10px 0;">📊 Updated Stats</h4>
            <ul style="margin: 0;">
                <li>Total clients assigned to {{ $coach->name }}: {{ $coach->users()->count() }}</li>
                <li>Client joined: {{ $user->created_at->format('F j, Y') }}</li>
                <li>Deassignment completed: {{ now()->format('F j, Y') }}</li>
            </ul>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('coaching.show', $coach->id) }}" class="btn">View Coach Details</a>
        </div>

        <p style="margin-top: 30px;">
            This is an automated notification. If you have any questions about this deassignment,
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
