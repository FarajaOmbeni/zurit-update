<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $formData['form_type'] ?? 'Money Quiz' }} Response</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .header {
            background: linear-gradient(45deg, #7c3aed, #eab308);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .section {
            margin-bottom: 25px;
            border-left: 4px solid #7c3aed;
            padding-left: 15px;
        }

        .section-title {
            color: #7c3aed;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .question {
            margin-bottom: 8px;
        }

        .answer {
            color: #059669;
            font-weight: 600;
        }

        .score-box {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }

        .contact-info {
            background: #fef3c7;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        li {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>{{ $formData['form_type'] ?? 'Money Quiz' }} Response</h2>
        <p>New submission from {{ $formData['fullName'] ?? ($formData['name'] ?? 'Anonymous') }}</p>
    </div>

    <div class="content">
        @if (isset($formData['form_type']) && $formData['form_type'] === 'Client Onboarding')
            {{-- CLIENT ONBOARDING FORM --}}
            <div class="section">
                <div class="section-title">📋 Client Onboarding Form</div>

                <h3>Section A: Personal & Family Profile</h3>
                <ul>
                    <li><strong>1. Full Name:</strong> {{ $formData['fullName'] ?? 'N/A' }}</li>
                    <li><strong>2. Date of Birth:</strong> {{ $formData['dateOfBirth'] ?? 'N/A' }}</li>
                    <li><strong>3. Marital Status:</strong> {{ $formData['maritalStatus'] ?? 'N/A' }}</li>
                    <li><strong>4. Children/Dependents:</strong> {{ $formData['childrenDependents'] ?? 'N/A' }}</li>
                    <li><strong>5. Occupation:</strong> {{ $formData['occupation'] ?? 'N/A' }}</li>
                    <li><strong>6. Monthly Income:</strong> {{ $formData['monthlyIncome'] ?? 'N/A' }}
                    </li>
                    <li><strong>7. Monthly Expenses:</strong> {{ $formData['monthlyExpenses'] ?? 'N/A' }}
                    </li>
                    <li><strong>8. Residential Status:</strong> {{ ucfirst($formData['residentialStatus'] ?? 'N/A') }}
                    </li>
                </ul>

                <h3>Section B: Financial Habits & Knowledge</h3>
                <ul>
                    <li><strong>9. How do you currently track your finances?</strong>
                        {{ $formData['financeTracking'] ?? 'N/A' }}</li>
                    <li><strong>10. Do you maintain a monthly or annual budget?</strong>
                        {{ ucfirst($formData['hasBudget'] ?? 'N/A') }}</li>
                    <li><strong>11. How comfortable are you with making financial decisions?</strong>
                        {{ $formData['financialComfort'] ?? 'N/A' }}</li>
                    <li><strong>12. Have you ever worked with a financial advisor?</strong>
                        {{ $formData['hadAdvisor'] ?? 'N/A' }}</li>
                    <li><strong>13. How comfortable are you discussing money matters?</strong>
                        {{ $formData['discussMoney'] ?? 'N/A' }}</li>
                </ul>

                <h3>Section C: Assets, Liabilities & Savings</h3>
                <ul>
                    @if (isset($formData['assets']) && is_array($formData['assets']))
                        <li><strong>14. Which of the following do you currently have?</strong>
                            {{ implode(', ', $formData['assets']) }}</li>
                    @else
                        <li><strong>14. Which of the following do you currently have?</strong>
                            {{ $formData['assets'] ?? 'N/A' }}</li>
                    @endif
                    <li><strong>15. Do you have any outstanding loans?</strong> {{ $formData['loans'] ?? 'N/A' }}</li>
                    <li><strong>16. How many months of expenses do you have in emergency savings?</strong>
                        {{ $formData['emergencyFund'] ?? 'N/A' }}</li>
                </ul>

                <h3>Section D: Goals & Priorities</h3>
                <ul>
                    <li><strong>17. Financial Goal 1:</strong> {{ $formData['goal1'] ?? 'N/A' }} (Timeline:
                        {{ $formData['goal1Timeline'] ?? 'N/A' }})</li>
                    <li><strong>18. Financial Goal 2:</strong> {{ $formData['goal2'] ?? 'N/A' }} (Timeline:
                        {{ $formData['goal2Timeline'] ?? 'N/A' }})</li>
                    <li><strong>19. Financial Goal 3:</strong> {{ $formData['goal3'] ?? 'N/A' }} (Timeline:
                        {{ $formData['goal3Timeline'] ?? 'N/A' }})</li>
                    <li><strong>20. What motivates you to achieve your financial goals?</strong>
                        {{ $formData['motivation'] ?? 'N/A' }}</li>
                    <li><strong>21. What are your biggest financial fears or concerns?</strong>
                        {{ $formData['fears'] ?? 'N/A' }}</li>
                </ul>

                <h3>Section E: Preferences & Expectations</h3>
                <ul>
                    @if (isset($formData['advisoryExpectations']) && is_array($formData['advisoryExpectations']))
                        <li><strong>22. What do you expect from an advisory relationship?</strong>
                            {{ implode(', ', $formData['advisoryExpectations']) }}</li>
                    @else
                        <li><strong>22. What do you expect from an advisory relationship?</strong>
                            {{ $formData['advisoryExpectations'] ?? 'N/A' }}</li>
                    @endif
                    <li><strong>23. How would you prefer to communicate?</strong>
                        {{ $formData['communicationPreference'] ?? 'N/A' }}</li>
                    <li><strong>24. Do you have any upcoming major life events?</strong>
                        {{ $formData['upcomingEvents'] ?? 'N/A' }}</li>
                </ul>
            </div>
        @elseif(isset($formData['form_type']) && $formData['form_type'] === 'Money Personality Assessment')
            {{-- MONEY PERSONALITY ASSESSMENT --}}
            <div class="section">
                <div class="section-title">🧠 Money Personality Assessment</div>
                <p><strong>Respondent:</strong> {{ $formData['name'] ?? 'Anonymous' }}</p>
                <h3>Detailed Responses:</h3>
                <ul>
                    @php
                        $personalityQuestions = [
                            1 => 'When you receive unexpected money, your first instinct is:',
                            2 => 'Budgeting for you is:',
                            3 => 'How do you feel about financial risk?',
                            4 => 'How often do you review your financial goals?',
                            5 => 'When faced with a financial decision, you:',
                            6 => 'What\'s your view on debt?',
                            7 => 'How organized are your financial records?',
                            8 => 'Your primary goal with money is:',
                            9 => 'How comfortable are you discussing money?',
                            10 => 'Your reaction to financial news or market trends is usually:',
                            11 => 'If you encounter financial difficulties, you:',
                            12 => 'Retirement planning for you is:',
                            13 => 'Spending money on yourself feels:',
                            14 => 'Your financial education comes primarily from:',
                            15 => 'Your reaction to an impulsive purchase is usually:',
                            16 => 'Credit cards to you are:',
                            17 => 'Your ideal financial scenario involves:',
                            18 => 'How do you feel about financial planning tools (apps/software)?',
                            19 => 'Investing to you feels:',
                            20 => 'When discussing finances with a partner or family, you:',
                        ];

                        $personalityOptions = [
                            1 => [
                                'A' => 'Save it immediately',
                                'B' => 'Plan exactly how you\'ll allocate it',
                                'C' => 'Treat yourself or others',
                                'D' => 'Invest it into something promising',
                                'E' => 'Worry about managing it properly',
                            ],
                            2 => [
                                'A' => 'Essential and strictly followed',
                                'B' => 'Carefully planned out each month',
                                'C' => 'A guideline you loosely follow',
                                'D' => 'Flexible, depending on investment opportunities',
                                'E' => 'Something you tend to avoid',
                            ],
                            3 => [
                                'A' => 'Prefer safety over risks',
                                'B' => 'Comfortable only after careful analysis',
                                'C' => 'Indifferent if it brings enjoyment',
                                'D' => 'Embrace it for potential gains',
                                'E' => 'Nervous and unsure',
                            ],
                            4 => [
                                'A' => 'Regularly, to ensure savings are growing',
                                'B' => 'Monthly or quarterly, following your plan',
                                'C' => 'Rarely, as long as bills are paid',
                                'D' => 'Frequently, adjusting for market trends',
                                'E' => 'Hardly ever',
                            ],
                            5 => [
                                'A' => 'Consider the safest option',
                                'B' => 'Research thoroughly before deciding',
                                'C' => 'Usually go with what feels good now',
                                'D' => 'Look for long-term potential benefits',
                                'E' => 'Procrastinate and avoid the decision',
                            ],
                            6 => [
                                'A' => 'Avoid it completely',
                                'B' => 'Acceptable if carefully managed',
                                'C' => 'A normal part of life',
                                'D' => 'Useful for leverage in investments',
                                'E' => 'Overwhelming and stressful',
                            ],
                            7 => [
                                'A' => 'Meticulously organized',
                                'B' => 'Well-structured with clear plans',
                                'C' => 'Basic and mostly digital',
                                'D' => 'Detailed regarding investments',
                                'E' => 'Unorganized or nonexistent',
                            ],
                            8 => [
                                'A' => 'Security and peace of mind',
                                'B' => 'Achieving clear financial milestones',
                                'C' => 'Enjoying life and experiences',
                                'D' => 'Growing wealth steadily',
                                'E' => 'Minimizing stress and complexity',
                            ],
                            9 => [
                                'A' => 'Prefer to keep private and controlled',
                                'B' => 'Comfortable when organized',
                                'C' => 'Open and casual',
                                'D' => 'Confident discussing financial opportunities',
                                'E' => 'Uncomfortable or avoidant',
                            ],
                            10 => [
                                'A' => 'Uninterested unless it impacts savings',
                                'B' => 'Interested for planning purposes',
                                'C' => 'Neutral or indifferent',
                                'D' => 'Highly engaged and proactive',
                                'E' => 'Overwhelmed or confused',
                            ],
                            11 => [
                                'A' => 'Rely on savings immediately',
                                'B' => 'Review and adjust your plan',
                                'C' => 'Cut back on luxuries temporarily',
                                'D' => 'Find new investment solutions',
                                'E' => 'Feel helpless or stuck',
                            ],
                            12 => [
                                'A' => 'Essential, with clear savings targets',
                                'B' => 'Well-planned and regularly reviewed',
                                'C' => 'Something to think about later',
                                'D' => 'Opportunity to grow wealth through investing',
                                'E' => 'Daunting and stressful',
                            ],
                            13 => [
                                'A' => 'Rare and unnecessary',
                                'B' => 'Acceptable within your plan',
                                'C' => 'Natural and enjoyable',
                                'D' => 'Justified if it leads to future gains',
                                'E' => 'Anxiety-inducing',
                            ],
                            14 => [
                                'A' => 'Family values and saving habits',
                                'B' => 'Books, courses, or professional advice',
                                'C' => 'Personal experiences',
                                'D' => 'Investment news and market analysis',
                                'E' => 'Sporadic or minimal sources',
                            ],
                            15 => [
                                'A' => 'Regretful',
                                'B' => 'Thoughtful and calculated',
                                'C' => 'Satisfied and happy',
                                'D' => 'Neutral—focus more on investments anyway',
                                'E' => 'Worried about money management',
                            ],
                            16 => [
                                'A' => 'A risk you prefer to avoid',
                                'B' => 'Useful when managed carefully',
                                'C' => 'Helpful and convenient',
                                'D' => 'Tools for strategic advantage',
                                'E' => 'Sources of stress',
                            ],
                            17 => [
                                'A' => 'A comfortable emergency fund',
                                'B' => 'Achieving clearly set financial goals',
                                'C' => 'Enough to enjoy life comfortably',
                                'D' => 'Growing investments and returns',
                                'E' => 'Not worrying about money',
                            ],
                            18 => [
                                'A' => 'Use them minimally to track savings',
                                'B' => 'Regularly utilize them for budgeting',
                                'C' => 'Occasionally glance at them',
                                'D' => 'Frequently use for investment tracking',
                                'E' => 'Avoid them',
                            ],
                            19 => [
                                'A' => 'Too risky or complex',
                                'B' => 'Important, with careful planning',
                                'C' => 'Unnecessary or confusing',
                                'D' => 'Exciting and necessary',
                                'E' => 'Intimidating',
                            ],
                            20 => [
                                'A' => 'Prefer clear and cautious plans',
                                'B' => 'Lead with detailed structure',
                                'C' => 'Are relaxed and spontaneous',
                                'D' => 'Encourage discussions about growth opportunities',
                                'E' => 'Avoid conversations if possible',
                            ],
                        ];
                    @endphp

                    @for ($i = 1; $i <= 20; $i++)
                        @if (isset($formData["q{$i}"]))
                            <li>
                                <strong>{{ $i }}. {{ $personalityQuestions[$i] }}</strong><br>
                                <span class="answer">{{ $formData["q{$i}"] }}.
                                    {{ $personalityOptions[$i][$formData["q{$i}"]] ?? 'Answer not found' }}</span>
                            </li>
                        @endif
                    @endfor
                </ul>

                <div class="score-box">
                    <p><strong>Assessment Summary:</strong></p>
                    <p>This assessment helps identify the client's financial personality type based on their responses
                        to behavioral and attitudinal questions about money management.</p>
                </div>
            </div>
        @elseif(isset($formData['form_type']) && $formData['form_type'] === 'Risk Tolerance Assessment')
            {{-- RISK TOLERANCE ASSESSMENT --}}
            <div class="section">
                <div class="section-title">⚖️ Risk Tolerance Assessment</div>
                <p><strong>Respondent:</strong> {{ $formData['name'] ?? 'Anonymous' }}</p>

                <div class="score-box">
                    <h3>🎯 Assessment Results:</h3>
                    <p><strong>Total Score:</strong> {{ $formData['total_score'] ?? 'N/A' }} / 80 points</p>
                    <p><strong>Risk Profile:</strong> <span
                            class="answer">{{ $formData['risk_profile'] ?? 'N/A' }}</span></p>
                </div>

                <h3>Detailed Responses:</h3>
                <ul>
                    @php
                        $riskQuestions = [
                            1 => 'What is your current age?',
                            2 => 'What is your employment/income stability like?',
                            3 => 'What is your monthly savings rate?',
                            4 => 'What percentage of your wealth is already invested?',
                            5 => 'What is your primary financial goal?',
                            6 => 'How would you describe your investment knowledge?',
                            7 => 'Have you previously made any investment decisions independently?',
                            8 => 'Do you currently follow financial or investment news?',
                            9 => 'Have you ever invested in high-risk products (crypto, stocks, startups)?',
                            10 => 'Do you understand the trade-off between risk and return?',
                            11 => 'If your investment portfolio dropped 20% in 6 months, what would you do?',
                            12 => 'How confident are you in sticking to a long-term plan despite market noise?',
                            13 => 'When making financial decisions, are you:',
                            14 => 'Have you ever sold an investment due to panic or fear?',
                            15 => 'How do you feel about financial uncertainty?',
                            16 => 'What is your preferred investment time horizon?',
                            17 => 'How often would you like to review your portfolio?',
                            18 => 'Are you more focused on:',
                            19 => 'If given an option, would you prefer:',
                            20 => 'Would you invest in long-term illiquid assets?',
                        ];

                        $riskOptions = [
                            1 => [4 => 'Below 30', 3 => '30–45', 2 => '46–60', 1 => 'Above 60'],
                            2 => [
                                4 => 'Very stable',
                                3 => 'Fairly stable',
                                2 => 'Irregular',
                                1 => 'Unstable or freelance',
                            ],
                            3 => [4 => 'Over 30% of income', 3 => '20–30%', 2 => '10–20%', 1 => 'Below 10% or none'],
                            4 => [4 => 'Over 60%', 3 => '40–60%', 2 => '20–40%', 1 => 'Less than 20%'],
                            5 => [
                                4 => 'Capital growth',
                                3 => 'Balanced growth and income',
                                2 => 'Regular income',
                                1 => 'Capital preservation',
                            ],
                            6 => [4 => 'Extensive', 3 => 'Moderate', 2 => 'Basic', 1 => 'None'],
                            7 => [4 => 'Frequently', 3 => 'Occasionally', 2 => 'Rarely', 1 => 'Never'],
                            8 => [4 => 'Daily', 3 => 'Weekly', 2 => 'Occasionally', 1 => 'Never'],
                            9 => [4 => 'Yes, often', 3 => 'Yes, a few times', 2 => 'Tried once or twice', 1 => 'Never'],
                            10 => [4 => 'Very well', 3 => 'Somewhat', 2 => 'Not much', 1 => 'Not at all'],
                            11 => [
                                4 => 'Buy more – good opportunity',
                                3 => 'Hold and wait',
                                2 => 'Re-evaluate and possibly reduce',
                                1 => 'Sell to prevent more loss',
                            ],
                            12 => [
                                4 => 'Very confident',
                                3 => 'Mostly confident',
                                2 => 'Sometimes unsure',
                                1 => 'Not confident',
                            ],
                            13 => [
                                4 => 'Quick and confident',
                                3 => 'Analytical but decisive',
                                2 => 'Hesitant',
                                1 => 'Prefer to defer',
                            ],
                            14 => [4 => 'Never', 3 => 'Rarely', 2 => 'Sometimes', 1 => 'Frequently'],
                            15 => [
                                4 => 'Comfortable with it',
                                3 => 'Acceptable if rewards are high',
                                2 => 'Prefer to avoid it',
                                1 => 'Very uncomfortable',
                            ],
                            16 => [4 => 'Over 10 years', 3 => '5–10 years', 2 => '3–5 years', 1 => 'Less than 3 years'],
                            17 => [4 => 'Annually', 3 => 'Bi-annually', 2 => 'Quarterly', 1 => 'Monthly'],
                            18 => [
                                4 => 'Wealth growth',
                                3 => 'Balanced growth and preservation',
                                2 => 'Regular income',
                                1 => 'Peace of mind',
                            ],
                            19 => [
                                4 => '10% gain with 5% loss',
                                3 => '6% gain with 2% loss',
                                2 => '3% gain with 0% loss',
                                1 => 'No gain, no risk',
                            ],
                            20 => [4 => 'Definitely', 3 => 'Likely', 2 => 'Hesitant', 1 => 'Never'],
                        ];
                    @endphp

                    @for ($i = 1; $i <= 20; $i++)
                        @if (isset($formData["q{$i}"]))
                            <li>
                                <strong>{{ $i }}. {{ $riskQuestions[$i] }}</strong><br>
                                <span class="answer">{{ $riskOptions[$i][$formData["q{$i}"]] ?? 'Answer not found' }}
                                    ({{ $formData["q{$i}"] }} points)</span>
                            </li>
                        @endif
                    @endfor
                </ul>

                <div class="score-box">
                    <p><strong>Risk Profile Guide:</strong></p>
                    <ul>
                        <li>65+ points: Aggressive Investor</li>
                        <li>45-64 points: Moderate Investor</li>
                        <li>25-44 points: Conservative Investor</li>
                        <li>Below 25: Ultra Conservative</li>
                    </ul>
                </div>
            </div>
        @else
            {{-- MONEY QUIZ - WEALTH SCORE ASSESSMENT --}}
            <div class="section">
                <div class="section-title">💰 Money Quiz - Wealth Score Assessment</div>
                <p><strong>Respondent:</strong> {{ $formData['fullName'] ?? ($formData['name'] ?? 'Anonymous') }}</p>

                <div class="score-box">
                    <h3>📊 Assessment Result:</h3>
                    @if (isset($formData['wealthLevel']) && isset($formData['totalScore']))
                        <p><strong>Wealth Level:</strong> {{ $formData['wealthLevel'] }}</p>
                        <p><strong>Total Score:</strong> {{ $formData['totalScore'] ?? 0 }}/{{ $formData['maxScore'] ?? 30 }}</p>
                        @if (isset($formData['resultMessage']))
                            <p><strong>Recommendation:</strong> {{ $formData['resultMessage'] }}</p>
                        @endif
                    @elseif (isset($formData['message']))
                        <p>{{ $formData['message'] }}</p>
                    @endif
                </div>

                <h3>Detailed Responses:</h3>
                <ul>
                    @php
                        $quizQuestions = [
                            'goalSetting1' => 'How often do you set financial goals?',
                            'goalSetting2' => 'Do you have a financial plan for achieving your goals?',
                            'investmentPlanning1' => 'Have you started investing?',
                            'investmentPlanning2' => 'How do you choose your investments?',
                            'debtManagement1' => 'How do you handle debt?',
                            'debtManagement2' => 'If you had to take a loan, what would be your reason?',
                            'budgetPlanning1' => 'How do you track your income and expenses?',
                            'budgetPlanning2' => 'How much of your income do you save each month?',
                            'financialKnowledge1' => 'What is the best way to build wealth over time?',
                            'financialKnowledge2' => 'What is an emergency fund used for?',
                        ];

                        $quizOptions = [
                            'goalSetting1' => [
                                3 => 'I set clear short-term and long-term financial goals',
                                2 => 'I think about financial goals but don\'t write them down',
                                0 => 'I don\'t set financial goals at all',
                            ],
                            'goalSetting2' => [
                                3 => 'Yes, I have a clear plan',
                                2 => 'I have an idea but no clear plan',
                                0 => 'No, I don\'t have a financial plan',
                            ],
                            'investmentPlanning1' => [
                                3 => 'Yes, I actively invest',
                                2 => 'No, but I plan to start soon',
                                0 => 'No, and I\'m not interested',
                            ],
                            'investmentPlanning2' => [
                                3 => 'I research and seek professional advice before investing',
                                2 => 'I follow trends or invest based on recommendations from friends',
                                0 => 'I invest randomly or don\'t invest at all',
                            ],
                            'debtManagement1' => [
                                3 => 'I avoid unnecessary debt and only borrow for good investments',
                                2 => 'I borrow occasionally but manage repayments well',
                                0 => 'I borrow often and struggle with repayments',
                            ],
                            'debtManagement2' => [
                                3 => 'To invest in a business or asset',
                                2 => 'To fund education or skill development',
                                0 => 'To buy personal items like a phone or clothes',
                            ],
                            'budgetPlanning1' => [
                                3 => 'I use a budgeting app or spreadsheet',
                                2 => 'I track my finances mentally but not regularly',
                                0 => 'I don\'t track my spending at all',
                            ],
                            'budgetPlanning2' => [3 => '20% or more', 2 => '10%-19%', 0 => 'Less than 10% or nothing'],
                            'financialKnowledge1' => [
                                3 => 'Saving and investing wisely',
                                2 => 'Earning more income but not necessarily saving',
                                0 => 'Depending on luck, lottery, or quick schemes',
                            ],
                            'financialKnowledge2' => [
                                3 => 'Unexpected expenses like medical bills and car repairs',
                                2 => 'Buying things on sale or last-minute purchases',
                                0 => 'Extra cash for parties and fun activities',
                            ],
                        ];
                    @endphp

                    @foreach ($quizQuestions as $key => $question)
                        @if (isset($formData[$key]))
                            <li>
                                <strong>{{ $question }}</strong><br>
                                <span class="answer">{{ $quizOptions[$key][$formData[$key]] ?? 'Answer not found' }}
                                    ({{ $formData[$key] }} points)</span>
                            </li>
                        @endif
                    @endforeach
                </ul>

                <div class="score-box">
                    <p><strong>Wealth Score Guide:</strong></p>
                    <ul>
                        <li>21+ points: 💎 Wealth Master</li>
                        <li>11-20 points: 📈 Growing Investor</li>
                        <li>Below 11: 🚦 Financial Starter</li>
                    </ul>
                </div>
            </div>
        @endif

        {{-- CONTACT INFORMATION SECTION --}}
        <div class="contact-info">
            <h3>📞 Contact Information</h3>
            <ul>
                <li><strong>Name:</strong> {{ $formData['fullName'] ?? ($formData['name'] ?? 'N/A') }}</li>
                <li><strong>Email:</strong> <a
                        href="mailto:{{ $formData['email'] ?? '' }}">{{ $formData['email'] ?? 'N/A' }}</a></li>
                <li><strong>Phone:</strong> <a
                        href="tel:{{ $formData['phone'] ?? '' }}">{{ $formData['phone'] ?? 'N/A' }}</a></li>
                @if (isset($formData['form_type']))
                    <li><strong>Form Type:</strong> {{ $formData['form_type'] }}</li>
                @endif
                <li><strong>Submission Time:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</li>
            </ul>
        </div>

        <div class="footer">
            <p><strong>Next Steps:</strong> Please follow up with this client within 24 hours to discuss their
                assessment results and potential advisory services.</p>
            <hr>
            <p>This email was generated automatically by the Zurit Consulting questionnaire system.</p>
        </div>
    </div>
</body>

</html>
