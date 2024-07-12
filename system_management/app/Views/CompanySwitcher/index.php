<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php $current_company = session()->get('current_company');
        echo $current_company->company_name ?? 'Select Company';
        ?>
    </a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
    <?php $companies = session()->get('companies');
    if ($companies !== null):
    foreach($companies as $company): ?>
        <li><a class="dropdown-item" href="<?= site_url('company-switcher/switch-company/' . $company->company_code) ?>"><?=  $company->company_name ?></a></li>
    <?php endforeach; ?>
    <?php endif; ?>
    </ul>
</li>