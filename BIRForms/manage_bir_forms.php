<?php

session_start();
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
echo $_SESSION['is_authenticated'];

// Check if the user is authorized. If not, redirect to the login page.
if (!isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true) {
    header('Location: bir_forms_management.php');
    exit;
}

// Your secure content or management interface goes here.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage BIR Forms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Manage BIR Forms</h2>
    <!-- Secure content for managing BIR forms goes here -->
</div>
<div id="app">
    <button @click="logout" class="btn btn-danger">Logout</button>
</div>
<script>
var app = new Vue({
    el: '#app',
    methods: {
        logout: function() {
            fetch('logout.php', {
                method: 'POST'
            })
            .then(response => {
                // Redirect to login page after successful logout
                window.location.href = 'bir_forms_management.php';
            })
            .catch(error => console.error('Error:', error));
        }
    }
});
</script>
</body>
</html>
