<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .certificate {
            background: white;
            width: 100%;
            max-width: 900px;
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .certificate::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
        }
        
        .certificate::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .title {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }
        
        .subtitle {
            font-size: 18px;
            color: #718096;
            font-weight: 300;
        }
        
        .content {
            text-align: center;
            margin: 60px 0;
        }
        
        .awarded-to {
            font-size: 20px;
            color: #4a5568;
            margin-bottom: 20px;
            font-weight: 400;
        }
        
        .recipient-name {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            display: inline-block;
            padding-bottom: 10px;
        }
        
        .course-info {
            font-size: 18px;
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 40px;
        }
        
        .course-title {
            font-weight: 600;
            color: #2d3748;
            font-size: 24px;
            margin: 10px 0;
        }
        
        .stats {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin: 40px 0;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            display: block;
        }
        
        .stat-label {
            font-size: 14px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 60px;
            padding-top: 40px;
            border-top: 2px solid #e2e8f0;
        }
        
        .date-section {
            text-align: left;
        }
        
        .certificate-id {
            text-align: right;
        }
        
        .date, .cert-id {
            font-size: 14px;
            color: #718096;
            margin-bottom: 5px;
        }
        
        .date-value, .cert-id-value {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
        }
        
        .signature-section {
            text-align: center;
        }
        
        .signature-line {
            width: 200px;
            height: 2px;
            background: #e2e8f0;
            margin: 20px auto 10px;
        }
        
        .signature-text {
            font-size: 14px;
            color: #718096;
        }
        
        .decorative-elements {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #f7fafc;
            border-radius: 15px;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="decorative-elements"></div>
        
        <div class="header">
            <div class="logo">Z</div>
            <h1 class="title">Certificate of Completion</h1>
            <p class="subtitle">Financial Literacy Program</p>
        </div>
        
        <div class="content">
            <p class="awarded-to">This is to certify that</p>
            <h2 class="recipient-name">{{ $user_name }}</h2>
            
            <div class="course-info">
                has successfully completed the course
                <div class="course-title">{{ $course_title }}</div>
                demonstrating proficiency in financial literacy concepts and practices.
            </div>
            
            <div class="stats">
                <div class="stat">
                    <span class="stat-value">{{ $overall_score }}%</span>
                    <span class="stat-label">Overall Score</span>
                </div>
                <div class="stat">
                    <span class="stat-value">{{ $total_subcourses }}</span>
                    <span class="stat-label">Modules Completed</span>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <div class="date-section">
                <div class="date">Date of Completion</div>
                <div class="date-value">{{ $completion_date }}</div>
            </div>
            
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-text">Zurit Financial Literacy</div>
            </div>
            
            <div class="certificate-id">
                <div class="cert-id">Certificate ID</div>
                <div class="cert-id-value">{{ $certificate_id }}</div>
            </div>
        </div>
    </div>
</body>
</html> 