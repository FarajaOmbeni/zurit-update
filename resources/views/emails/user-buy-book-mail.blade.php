<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #4a148c;">📦 Thank You for Your Order!</h2>
        <p>Dear {{ $name }},</p>

        <p>We have received your order for the book:</p>

        <blockquote style="background-color: #f3f4f6; padding: 10px; border-left: 4px solid #4a148c; font-style: italic;">
            {{ $book_title }}
        </blockquote>

        <p>Our team is currently processing your order. You will receive further details shortly via this email address: <strong>{{ $email }}</strong> or this phone number: <strong>{{ $phone }}</strong>.</p>

        <p>If you have any questions or need to make changes, feel free to reply to this email.</p>

        <p style="margin-top: 30px;">Thank you for choosing us!</p>

        <p>Warm regards,<br>Zurit Consulting</p>
    </div>
</body>
</html>
