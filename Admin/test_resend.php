<?php
// Test: send to the Resend account owner's email (the only allowed recipient in test mode)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.resend.com/emails");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "from" => "onboarding@resend.dev",
    "to" => "zianiiman060@gmail.com",
    "subject" => "Test Parc Auto",
    "html" => "<p>Test email depuis le système Parc Auto !</p>"
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer re_GLYWxyUA_HqDCD7GiARbp8WkRgj8CQPUu",
    "Content-Type: application/json"
]);
$result = curl_exec($ch);
curl_close($ch);
echo "Response: " . $result;
