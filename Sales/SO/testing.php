<?php 
    if(!isset($_SESSION)) {
        session_start();
    }
    // print_r($_POST);
    // print_r($_SESSION);

    $_SESSION['pageid'] = "BIRForms";

    include("../../Connection/connection_string.php");
    include('../../include/denied.php');
    include('../../include/access.php');

$UrlBase = str_replace("Components/assets/", "laravel-backend/public", $AttachUrlBase);
$apiUrl = $UrlBase . "/api/hello";

// Initialize cURL
$ch = curl_init();

// Set the URL
curl_setopt($ch, CURLOPT_URL, $apiUrl);

// Return the response instead of outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Disable SSL verification
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Execute the request
$response = curl_exec($ch);

// Check for cURL errors
if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    die("cURL Error: $error");
}

// Close cURL
curl_close($ch);

// Decode the JSON response
$data = json_decode($response, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Laravel</title>
</head>
<body style="text-align: center; height: 100vh; display: flex; justify-content: center; align-items: center;">
    <h1>API Response: <?php echo htmlspecialchars($data['message']); ?></h1>
</body>
</html>