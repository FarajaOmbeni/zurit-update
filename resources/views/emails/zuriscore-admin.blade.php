<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>M-Pesa Statement Generated – Admin Notification</title>
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
            margin-top: 0;
        }

        p {
            color: #555555;
            line-height: 1.6;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .info-table th,
        .info-table td {
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #eeeeee;
            font-size: 14px;
        }

        .download-link {
            word-break: break-all;
            color: #007bff;
            text-decoration: none;
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
            <img src="https://zuritconsulting.com/images/home/zurit_no_bg.png" alt="Zurit Consulting Logo">
        </div>

        <h2>Hello {{ $adminName ?? 'Zurit Team' }},</h2>

        <p>
            A new <strong>M-Pesa statement analysis</strong> has been generated successfully.
            Below are the details:
        </p>

        <table class="info-table">
            <tr>
                <th>Client Name</th>
                <td>{{ $clientName }}</td>
            </tr>
            <tr>
                <th>Report Months</th>
                <td>{{ $reportMonths ?? now() }}</td>
            </tr>
            <tr>
                <th>Generated At</th>
                <td>{{ $reportDate ?? now() }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>© {{ date('Y') }} Zurit Consulting. All rights reserved.</p>
            <div class="footer-logo">
                <img src="https://zuritconsulting.com/images/home/zurit_no_bg.png" alt="Zurit Consulting Logo">
            </div>
        </div>
    </div>
</body>

</html>
