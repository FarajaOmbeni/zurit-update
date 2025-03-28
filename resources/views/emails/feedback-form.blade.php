<!DOCTYPE html>
<html>

<head>
    <title>Feedback Form Submission</title>
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
        <p><b>{{ $name }}</b></p>
        <hr>
        <p><b>Question: </b>Please rate the overall organization and logistics of the training sessions (e.g., venue,
            scheduling, facilities).</p>
        <p><b>Answer: </b>{{ $venue }}/5</p>
        <hr>
        <p><b>Question: </b>How satisfied are you with the clarity and comprehensiveness of the topics covered during
            the training sessions?</p>
        <p><b>Answer: </b>{{ $comprehensiveness }}/5</p>
        <hr>
        <p><b>Question: </b>How would you rate the relevance of the topics covered in our training sessions to your
            financial goals and needs?
        </p>
        <p><b>Answer: </b>{{ $relevance }}/5</p>

        <hr>

        <p><b>Question: </b>How likely are you to recommend Zurit Consulting's trainings to others based on your
            experience?
        </p>
        <p><b>Answer: </b>{{ $recommendation }}/5</p>
        <hr>
        <p><b>Question: </b>How likely are you to attend other Zurit Consulting's trainings based on your experience?
        </p>
        <p><b>Answer: </b>{{ $return_client }}/5</p>
        <hr>
        <p><b>Question: </b>Please rate the value for money of the financial training and services provided by Zurit
            Consulting.
        </p>
        <p><b>Answer: </b>{{ $value_for_money }}/5</p>
        <hr>
        <p><b>Question: </b>What aspects of our training sessions did you find most valuable?
        </p>
        <p><b>Answer: </b>{{ $valuable_aspect }}</p>
        <hr>
        <p><b>Question: </b>Is there anything specific that you feel could be improved about our training programs?
        </p>
        <p><b>Answer: </b>{{ $improvement }}</p>
        <hr>
        <p><b>Question: </b>Are there any additional topics or areas of interest you would like to see covered in future
            training sessions?
        </p>
        <p><b>Answer: </b>{{ $suggestion }}</p>
        <hr>

        <p><b>Question: </b>Who was your favourite trainor/trainors?
        </p>
        <p><b>Answer: </b>{{ $fav_trainor }}</p>
        <hr>

        <div class="logo">
            <img src="zuritconsulting.com/public_html/home_res/img/logo-white3.webp" alt="Logo">
        </div>


        <div class="footer">
            <p>This is a system generated email, please do not reply directly.</p>
            <h4>© 2024 Zurit Consulting. All rights reserved.</h4>
        </div>
    </div>
</body>

</html>
