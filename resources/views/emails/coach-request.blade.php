<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Coach Request</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 32px;">
        <h2 style="color: #6D28D9; margin-bottom: 24px;">New Coach Request</h2>
        <p style="font-size: 16px; color: #333;">
            A new user has requested to be assigned a coach.
        </p>
        <table style="margin-top: 24px; font-size: 16px;">
            <tr>
                <td style="font-weight: bold; padding-right: 12px;">Name:</td>
                <td>{{ $userName }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-right: 12px;">Email:</td>
                <td>{{ $userEmail }}</td>
            </tr>
        </table>
        <p style="margin-top: 32px; color: #555;">
            Please review this request and assign a suitable coach to the user.
        </p>
        <p style="margin-top: 32px; color: #888; font-size: 14px;">
            This is an automated notification from the Zurit Consulting platform.
        </p>
    </div>
</body>
</html>
