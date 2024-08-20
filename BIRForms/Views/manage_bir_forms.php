<?php
require 'Controllers/PinController.php';

$controller = new PinController();

// Check if the logout parameter is set
if (isset($_GET['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to pin_access_view.php
    header('Location: pin_access_view.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage BIR Forms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Manage BIR Forms</h2>
    <!-- Secure content for managing BIR forms goes here -->
</div>
<div class="container">
    <a href="?logout=1" class="btn btn-danger">Logout</a>
</div>
</body>
</html>