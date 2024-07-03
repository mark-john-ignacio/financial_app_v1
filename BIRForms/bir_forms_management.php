<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIR Forms Management</title>
    <!-- Replace Water.css with Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div id="pinAuthApp" class="card">
        <div class="card-body">
          <h5 class="card-title text-center">BIR Forms Management</h5>
          <form v-on:submit.prevent="checkPin">
            <div class="form-group mb-3">
              <input type="password" v-model="pin" class="form-control" placeholder="Enter Pin Code">
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <p v-if="authFailed" class="text-danger mt-2">Authentication Failed. Please try again.</p>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
  new Vue({
    el: '#pinAuthApp',
    data: {
      pin: '',
      correctPin: '1234', // This should ideally come from a secure source or be verified server-side
      authFailed: false
    },
    methods: {
      checkPin: function() {
          if(this.pin === this.correctPin) {
              // Assuming authentication is successful
              alert('Authentication Successful');
              // Set a session variable to indicate authentication
              <?php $_SESSION['is_authenticated'] = true; ?>
              // Redirect to the secure page
              window.location.href = 'manage_bir_forms.php';
          } else {
              this.authFailed = true;
              this.pin = ''; // Clear pin input
          }
      }
    }
  });
</script>