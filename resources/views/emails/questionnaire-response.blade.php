<h2>Greetings Zurit</h2>
<p>My name is <strong>{{ $formData['name'] ?? 'N/A' }}</strong> and I have taken the money quiz.</p>
<p>Below are the results i got: </p>
<p>{{ $formData['message'] ?? 'N/A' }}</p>
<hr>
<p>Kindly contact me through <strong>{{ $formData['phone'] ?? 'N/A' }}</strong> or <strong>{{ $formData['email'] ?? 'N/A' }}</strong></p>

<hr>

<p>Thank you!</p>