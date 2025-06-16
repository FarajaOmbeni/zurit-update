<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
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
        <h2>{{ $title }}</h2>
        {!! $content !!}
        <div class="footer">
            <h4>© 2025 Zurit Consulting. All rights reserved.</h4>
            <img src="{{ asset('images/home/zurit_white_bg.webp') }}" alt="Zurit Consulting Logo">
        </div>
    </div>
</body>

</html>
