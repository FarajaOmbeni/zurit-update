<!DOCTYPE html>
<html>

<head>
    <title>Contact Form Submission</title>
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
            width: 40%;
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
        <p><strong>Name:</strong> {{ $name }}</p>
        <p><strong>Email:</strong> {{ $email }}</p>
        <p><strong>Message:</strong> {{ $userMessage }}</p>
        <div class="footer">
            <div class="logo">
                <img src="zuritconsulting.com/public_html/home_res/img/logo-white3.webp" alt="Logo">
            </div>
            <p>This is a system generated email, please do not reply directly.</p>
            <h4>© 2024 Zurit Consulting. All rights reserved.</h4>
        </div>
    </div>
</body>

</html>
