<?php

$apiKey = '523f06959367d033-602c9c1035ecf184-307c81fa0e334f96';
$recipient = '+639266138981';
$message = 'Hello, this is a test message!';

$url = 'https://api.viber.com/send_message';

$data = [
    'receiver' => $recipient,
    'text' => $message,
];

$headers = [
    'Content-Type: application/json',
    'X-Viber-Auth-Token: ' . $apiKey,
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo 'Response: ' . $response;
}

curl_close($ch);

?>