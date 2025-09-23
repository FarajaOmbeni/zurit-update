<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Purchase Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f8f8; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #4a148c;">E‑Learning Purchase Confirmation</h2>
        <p>Hello {{ $name }},</p>
        <p>Thank you for your purchase. Your lifetime access to the course is now active:</p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="font-weight: bold; padding: 8px; background-color: #f0f0f0;">Course</td>
                <td style="padding: 8px;">{{ $courseTitle }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 8px; background-color: #f0f0f0;">Access</td>
                <td style="padding: 8px;">Lifetime</td>
            </tr>
        </table>

        <p style="margin-top: 30px;">Start learning anytime — visit your E‑Learning dashboard to begin.</p>

        <p>Regards,<br>Zurit Consulting</p>
    </div>
</body>
</html>

