<!DOCTYPE html>
<html>

<head>
    <title>Feedback Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        p {
            line-height: 1.6;
            color: #333;
        }

        b {
            color: #222;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }

        .logo {
            margin: 30px auto 20px auto;
            text-align: center;
        }

        .logo img {
            width: 120px;
            height: auto;
        }

        .footer {
            font-size: 12px;
            text-align: center;
            margin-top: 30px;
            color: #777;
        }

        h4 {
            margin-top: 10px;
            font-weight: normal;
        }
    </style>

</head>

<body>
    <div class="email-container">
        <h3><b>Name: {{ $userName }}</b></h3>
        <p><b>{{ $eventName }}</b></p>
        <hr>
        <p><b>Question: </b>Please rate the overall organization and logistics of the training sessions (e.g., venue,
            scheduling, facilities).</p>
        <p><b>Answer: </b>{{ $logisticsRating }}/5</p>
        <hr>
        <p><b>Question: </b>How satisfied are you with the clarity and comprehensiveness of the topics covered during
            the training sessions?</p>
        <p><b>Answer: </b>{{ $clarityRating }}/5</p>
        <hr>
        <p><b>Question: </b>How would you rate the relevance of the topics covered in our training sessions to your
            financial goals and needs?
        </p>
        <p><b>Answer: </b>{{ $relevanceRating }}/5</p>

        <hr>

        <p><b>Question: </b>How likely are you to recommend Zurit Consulting's trainings to others based on your
            experience?
        </p>
        <p><b>Answer: </b>{{ $recommendationLikelihood }}/5</p>
        <hr>
        <p><b>Question: </b>How likely are you to attend other Zurit Consulting's trainings based on your experience?
        </p>
        <p><b>Answer: </b>{{ $attendanceLikelihood }}/5</p>
        <hr>
        <p><b>Question: </b>Please rate the value for money of the financial training and services provided by Zurit
            Consulting.
        </p>
        <p><b>Answer: </b>{{ $valueForMoney }}/5</p>
        <hr>
        <p><b>Question: </b>What aspects of our training sessions did you find most valuable?
        </p>
        <p><b>Answer: </b>{{ $mostValuable }}</p>
        <hr>
        <p><b>Question: </b>Is there anything specific that you feel could be improved about our training programs?
        </p>
        <p><b>Answer: </b>{{ $areaOfImprovement }}</p>
        <hr>
        <p><b>Question: </b>Are there any additional topics or areas of interest you would like to see covered in future
            training sessions?
        </p>
        <p><b>Answer: </b>{{ $topicSuggestion }}</p>
        <hr>

        <p><b>Question: </b>Who was your favourite trainor/trainors?
        </p>
        <p><b>Answer: </b>{{ $favoriteSpeaker }}</p>
        <hr>

        <div class="logo">
            <img src="{{ asset('images/home/zurit_white_bg.webp') }}" alt="Logo">
        </div>



        <div class="footer">
            <p>This is a system generated email, please do not reply directly.</p>
            <h4>© 2025 Zurit Consulting. All rights reserved.</h4>
        </div>
    </div>
</body>

</html>
