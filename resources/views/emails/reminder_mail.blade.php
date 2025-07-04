<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Your Investments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f9fc;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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

        h3 {
            color: #555555;
        }

        p {
            color: #666666;
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

        .footer-logo {
            margin-top: 10px;
        }

        .footer-logo img {
            width: 100px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="logo">
            <img src="https://zuritconsulting.com//images/home/zurit_no_bg.png" alt="Zurit Consulting Logo">
        </div>

        <h2>Hello {{ $user->name }},</h2>
        <h3>It's a great day to grow your investments!</h3>
        <p>
            We're reaching out from Zurit Consulting to gently remind you to review and update your investment
            portfolio.
            Keeping it current helps ensure you're on track to meet your financial goals.
        </p>
        <p>
            You can easily log in to your account and manage your portfolio by clicking the button below. And if you
            have
            any questions, our team is always here to help.
        </p>

        <a class="cta-button" href="https://zuritconsulting.com/user/invest">Update My Portfolio</a>

        <div class="footer">
            <p>© {{ date('Y') }} Zurit Consulting. All rights reserved.</p>
            <div class="footer-logo">
                <img src="https://zuritconsulting.com//images/home/zurit_no_bg.png" alt="Zurit Consulting Logo">
            </div>
        </div>
    </div>
</body>

</html>
