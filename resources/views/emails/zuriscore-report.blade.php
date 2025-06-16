<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>M-Pesa Statement Ready</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8fb;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 120px;
            height: auto;
        }

        h2 {
            color: #333333;
        }

        p {
            color: #555555;
            line-height: 1.6;
        }

        .cta-button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #999999;
            margin-top: 30px;
        }

        .footer-logo img {
            width: 100px;
            height: auto;
            margin-top: 10px;
        }

        hr {
            border: 0;
            height: 1px;
            background-color: #eeeeee;
            margin: 30px 0;
        }
    </style>
</head>

<body>
    <div class="email-container">

        <div class="logo">
            <img src="https://zuritconsulting.com//images/home/zurit_no_bg.png" alt="Zurit Consulting Logo">
        </div>

        <h2>Dear {{ $name ?? 'Ombeni' }},</h2>

        <p>🎉 Bingo! Your M-Pesa statement analysis is ready for download.</p>

        <p>
            You can access your report securely by clicking the button below:
        </p>

        <a class="cta-button" href="{{ $reportUrl ?? 'o' }}">Download Now</a>

        <p><em>(Please note: This link will expire in 24 hours.)</em></p>

        <hr>

        <p>
            💬 Want to talk to our wealth coach? Simply reply to this email or call +254 759 092 412 and we'll gladly
            set up a session to help you navigate your finances.
        </p>

        <p>Warm regards,<br> The Zurit Consulting Team</p>

        <div class="footer">
            <p>© {{ date('Y') }} Zurit Consulting. All rights reserved.</p>
            <div class="footer-logo">
                <img src="https://zuritconsulting.com//images/home/zurit_no_bg.png" alt="Zurit Consulting Logo">
            </div>
        </div>
    </div>
</body>

</html>
