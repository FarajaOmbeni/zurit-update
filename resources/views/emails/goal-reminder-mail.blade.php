<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Goal Contribution Reminder</title>
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
            background-color: #6f42c1;
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
    </style>
</head>

<body>
    <div class="email-container">
        <div class="logo">
            <img src="https://zuritconsulting.com//images/home/zurit_no_bg.png" alt="Zurit Consulting Logo">
        </div>

        <h2>Hello {{ $user->name }},</h2>
        <p>
            Just a quick reminder from the Zurit Consulting team — every step counts when it comes to reaching your
            financial goals.
        </p>
        <p>
            Have you made a contribution toward your goal this week? Your Goal Setting Tool makes it easy to track and
            stay committed. Small, consistent progress leads to big achievements.
        </p>
        <p>
            Take a moment now to contribute and see how close you are to your target!
        </p>

        <a target="_blank" class="cta-button" href="https://zuritconsulting.com/user/goal">Contribute Now</a>

        <div class="footer">
            <p>© {{ date('Y') }} Zurit Consulting. All rights reserved.</p>
            <div class="footer-logo">
                <img src="https://zuritconsulting.com//images/home/zurit_no_bg.png" alt="Zurit Consulting Logo">
            </div>
        </div>
    </div>
</body>

</html>
