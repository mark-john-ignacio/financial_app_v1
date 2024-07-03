<?php
require 'Controllers/PinController.php';
$controller = new PinController();

// Handle pin submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pin'])) {
    $isAuthenticated = $controller->verifyPin($_POST['pin']);
    if ($isAuthenticated) {
        include 'Views/manage_bir_forms.php';
        exit;
    } else {
        $authFailed = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIR Forms Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title text-center">BIR Forms Management</h5>
          <form method="POST">
            <div class="form-group mb-3">
              <input type="password" name="pin" class="form-control" placeholder="Enter Pin Code" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <?php if (isset($authFailed) && $authFailed): ?>
            <p class="text-danger mt-2">Authentication Failed. Please try again.</p>
            <?php endif; ?>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>