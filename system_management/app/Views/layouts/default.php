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
    <script src="<?= base_url("assets/js/jquery-3.7.1.min.js")?>"></script>
    <script src="<?= base_url("assets/js/dataTables.js")?>"></script>
    <script src="<?= base_url("assets/js/bootstrap.bundle.min.js")?>"></script>
    <script src="<?= base_url("assets/js/fontawesome.js")?>"></script>


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
            <li class="nav-item">
                <a href="<?= site_url("bir-forms/year-form")?>" class="nav-link">BIR Forms</a>
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