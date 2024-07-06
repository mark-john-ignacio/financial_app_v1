<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $this->renderSection("title") ?></title>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= site_url("bir-year-form")?>">BIR Forms</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (session()->get('pin_verified')): ?>
            <li class="nav-item">
                <a href="<?= site_url("change-pin")?>" class="nav-link">Change Pin</a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url("logout-pin")?>" class="nav-link">Logout</a>
            </li>
        <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?= "Environment: ". getenv("CI_ENVIRONMENT")?>
<br>
<?= "Base URL: " . base_url();?>
<br>
<?= "Site URL: " . site_url();?>
<br>
<?= "Current URI: " . $_SERVER['REQUEST_URI']?>
<?= $this->renderSection("content") ?>


</body>
</html>