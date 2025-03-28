<h1>Zurit Questionnaire Response</h1>

<p>Here are the responses from the questionnaire:</p>

<hr>

<h2>User Details</h2>
<p><strong><Kbd>Name</Kbd>:</strong></p>
<p>{{ $formData['name'] ?? 'N/A' }}</p>
<p><strong><Kbd>Phone Number</Kbd>:</strong></p>
<p>{{ $formData['phone'] ?? 'N/A' }}</p>
<p><strong><Kbd>Email</Kbd>:</strong></p>
<p>{{ $formData['email'] ?? 'N/A' }}</p>

<hr>

<h2>1. Goal Setting</h2>
<p><strong>Short-term financial goal:</strong> {{ $formData['short_term_goal'] ?? 'N/A' }} @if(isset($formData['short_term_goal_other_specify'])) - {{ $formData['short_term_goal_other_specify'] }} @endif</p>
<p><strong>Long-term financial goal:</strong> {{ $formData['long_term_goal'] ?? 'N/A' }} @if(isset($formData['long_term_goal_other_specify'])) - {{ $formData['long_term_goal_other_specify'] }} @endif</p>
<p><strong>Motivation system:</strong> {{ $formData['motivation_system'] ?? 'N/A' }}</p>

<hr>

<h2>2. Investment Planning</h2>
<p><strong>Started investing:</strong> {{ $formData['started_investing'] ?? 'N/A' }}</p>
<p><strong>Investment option interest:</strong> {{ $formData['investment_option'] ?? 'N/A' }} @if(isset($formData['investment_option_other_specify'])) - {{ $formData['investment_option_other_specify'] }} @endif</p>
<p><strong>Investment risk comfort:</strong> {{ $formData['investment_risk_comfort'] ?? 'N/A' }}</p>
<p><strong>Investment challenge:</strong> {{ $formData['investment_challenge'] ?? 'N/A' }}</p>

<hr>

<h2>3. Debt Management</h2>
<p><strong>Outstanding debt:</strong> {{ $formData['outstanding_debt'] ?? 'N/A' }}</p>
<p><strong>Loan reason:</strong> {{ $formData['loan_reason'] ?? 'N/A' }}</p>
<p><strong>Debt management approach:</strong> {{ $formData['debt_management_approach'] ?? 'N/A' }}</p>

<hr>

<h2>4. Budget Planning</h2>
<p><strong>Income/Expense tracking:</strong> {{ $formData['income_expense_tracking'] ?? 'N/A' }}</p>
<p><strong>Monthly savings decision:</strong> {{ $formData['monthly_savings_decision'] ?? 'N/A' }}</p>
<p><strong>Budgeting challenge:</strong> {{ $formData['budgeting_challenge'] ?? 'N/A' }}</p>

<hr>

<h2>Final Thought</h2>
<p><strong>Personalized tips:</strong> {{ $formData['personalized_tips'] ?? 'N/A' }}</p>

<hr>

<h2>Bonus Question</h2>
<p><strong>Ksh 100,000 action:</strong></p>
<p>{{ $formData['bonus_question_answer'] ?? 'N/A' }}</p>

<hr>

<p>Thank you!</p>