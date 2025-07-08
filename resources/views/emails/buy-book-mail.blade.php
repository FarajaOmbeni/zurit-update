<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Book Order</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f8f8; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
        <h2 style="color: #4a148c;">📚 New Book Order Notification</h2>
        <p>Hello Admin,</p>
        <p>A new book order has just been placed. Below are the details:</p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="font-weight: bold; padding: 8px; background-color: #f0f0f0;">Name</td>
                <td style="padding: 8px;">{{ $name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 8px; background-color: #f0f0f0;">Email</td>
                <td style="padding: 8px;">{{ $email }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 8px; background-color: #f0f0f0;">Phone</td>
                <td style="padding: 8px;">{{ $phone }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 8px; background-color: #f0f0f0;">Address</td>
                <td style="padding: 8px;">{{ $address }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 8px; background-color: #f0f0f0;">Book Title</td>
                <td style="padding: 8px;">{{ $book_title }}</td>
            </tr>
        </table>

        <p style="margin-top: 30px;">Kindly follow up and process the order as soon as possible.</p>

        <p>Regards,<br>Zurit Consulting</p>
    </div>
</body>
</html>
