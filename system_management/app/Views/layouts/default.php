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

    <script src="<?= base_url("assets/js/jquery-3.7.1.min.js")?>"></script>
    <script src="<?= base_url("assets/js/dataTables.js")?>"></script>
    <script src="<?= base_url("assets/js/bootstrap.bundle.min.js")?>"></script>
    <script src="<?= base_url("assets/js/fontawesome.js")?>"></script>

    <script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>



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
                </ul>
            </li>
            
            <li class="nav-item">
                <a href="<?= site_url("users-license")?>" class="nav-link">Users License</a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url("nav-menus")?>" class="nav-link">Nav Menus</a>
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

<?= $this->renderSection("content") ?>


</body>
</html>

<?= $this->renderSection("scripts") ?>