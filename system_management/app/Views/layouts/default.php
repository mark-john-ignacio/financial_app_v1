<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $this->renderSection("title") ?></title>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css"> -->
    <link rel="stylesheet" href="<?= base_url("assets/css/dataTables.dataTables.css")?>" />
    <link href="<?= base_url("assets/css/bootstrap.min.css")?>" rel="stylesheet">

    <link href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@5/dark.css" />

    <script src="<?= base_url("assets/js/jquery-3.7.1.min.js")?>"></script>
    <script src="<?= base_url("assets/js/dataTables.js")?>"></script>
    <script src="<?= base_url("assets/js/bootstrap.bundle.min.js")?>"></script>
    <script src="<?= base_url("assets/js/fontawesome.js")?>"></script>

    <script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>


<style>
html {
  position: relative;
  min-height: 100%;
  padding-bottom:50px;
}
body {
  margin-bottom: 160px;
}
footer {
  position: absolute;
  bottom: 0;
  width: 100%;
  height: 30px;
}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">HiddenSysMgt</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (session()->get('pin_verified')): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    BIR Forms
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li class="nav-item">
                        <a href="<?= site_url("bir-forms/year-form")?>" class="nav-link">Year-Form</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url("bir-forms/form")?>" class="nav-link">Form</a>
                    </li>
                    <li>
                        <a href="<?= site_url("bir-forms/reporting-period") ?>" class="nav-link">Reporting Period Type</a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item">
                <a href="<?= site_url("users-license")?>" class="nav-link">Users License</a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url("nav-menus")?>" class="nav-link">Nav Menus</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Data Sync & Upload
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li class="nav-item">
                        <a href="<?= site_url("customers")?>" class="nav-link">Customers</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url("items")?>" class="nav-link">Items</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url("suppliers")?>" class="nav-link">Suppliers</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url("item-code-sync")?>" class="nav-link">Item Code Sync</a>
                    </li>
                </ul>
            </li>
            </ul>
            <div class="d-flex">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?= $this->include("CompanySwitcher/index") ?>
                    <li class="nav-item">
                        <a href="<?= site_url("change-pin")?>" class="nav-link">Change Pin</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url("logout-pin")?>" class="nav-link">Logout</a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-1">
    <div class="row">
        <div class="col-md-6">
            <?php if (session()->has('message')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <?= session('message') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul>
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->renderSection("content") ?>

<footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
    <div class="col-md-4 d-flex align-items-center">
    <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1 mx-2">
        <svg class="bi" width="16" height="16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
            <path d="M0 0h1v15h15v1H0V0zm4 13h1V6H4v7zm3 0h1V2H7v11zm3 0h1V9h-1v4zm3 0h1V5h-1v8z"/>
        </svg>
    </a>
    <span class="mb-3 mb-md-0 text-muted">© 2024 MyxFinancials, Inc</span>
    </div>
</footer>

<?= $this->renderSection("scripts") ?>

<?php 
$session = \Config\Services::session();

if ($session->has('swal') && !empty($session->get('swal'))): 
    list($title, $description, $type) = explode(',', $session->get('swal')); 
    $session->remove('swal'); 
?>
    <script>
        Swal.fire({
            title: '<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>',
            text: '<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>',
            icon: '<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>',
        });
    </script>
<?php endif; ?>
</body>
</html>

