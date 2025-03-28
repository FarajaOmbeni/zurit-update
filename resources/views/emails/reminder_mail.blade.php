<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Managing Investements</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .logo {
            margin-left: 100px;
            margin-bottom: 20px;
            width: 20%;
            height: auto;
        }

        .footer {
            font-size: 12px;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-container">
            <h2>Hello {{ $user->name }},</h2>
            <h3>A new day to update your portfolio, and we are here to help!</h3>
            <p>From the Zurit Consulting team, I am writing this email to remind you to update your investment portfolio
                so that you can maximize your returns. Log on to our website to track and manage your portfolio. Also,
                do not hesitate to give us a call for any queries. <br> Click <a
                    href="https://zuritconsulting.com/user_budgetplanner">here</a> to update your portfolio</p>
            <div class="footer">
                <div class="logo">
                    <img src="{{ asset('home_res/img/logo-white3.webp') }}" alt="Zurit Consulting Logo">
                </div>
                <h4>© {{ date('Y') }} Zurit Consulting. All rights reserved.</h4>
            </div>
        </div>
        <div class="footer">
            <div class="logo">
                <img src="zuritconsulting.com/public_html/home_res/img/logo-white3.webp" alt="Logo">
            </div>
            <h4>© 2024 Zurit Consulting. All rights reserved.</h4>
        </div>
    </div>
</body>

</html>
