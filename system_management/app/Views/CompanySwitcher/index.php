<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Current Company
    </a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
    <?php $companies = session()->get('companies');
    foreach($companies as $company): ?>
        <li><a class="dropdown-item" href="<?= site_url("company-switcher/{$company->company_code}")?>"><?= $company->company_name ?></a></li>
    <?php endforeach; ?>
    </ul>
</li>