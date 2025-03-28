<!DOCTYPE html>
<html>
<head>
    <title>Financial Assistance Request</title>
</head>
<body>
    <h2>New Financial Request</h2>
    <p>A user has requested financial assistance/advice:</p>
    
    <ul>
        <li><strong>User Name:</strong> {{ $userName }}</li>
        <li><strong>User Email:</strong> {{ $userEmail }}</li>
        <li><strong>Request Type:</strong> {{ $requestType === 'help' ? 'Financial Assistance' : 'Portfolio Optimization' }}</li>
    </ul>

    <p>Please reach out to the user as soon as possible.</p>
</body>
</html>