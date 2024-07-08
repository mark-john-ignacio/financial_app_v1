<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->post('/verify-pin', 'PinController::verifyPin');

$routes->group('', ['filter' => 'pin_verified'], function ($routes) {

    $routes->get('/change-pin', 'PinController::changePin');
    $routes->post('/set-pin', 'PinController::setPin');
    $routes->get('/logout-pin', 'PinController::logout');

    $routes->group('bir-forms', function ($routes) {
        $con_path = 'BIRForms\\BIRYearFormController';
        $routes->get('year-form/associations', $con_path . '::associations');
        $routes->resource('year-form', ['controller' => $con_path]);
        $routes->post('year-form/new', $con_path . '::new');
    });

    $routes->group('', function ($routes) {
        $con_path = 'UsersLicense\\UsersLicenseController';
        $routes->resource('users-license', ['controller' => $con_path]);
    });

    $routes->group('nav-menus', function ($routes) {
        $con_path = 'NavMenus\\NavMenuController';
        $routes->resource('', ['controller' => $con_path]);
        $routes->get('get-menus', $con_path . '::getMenus');
        $routes->post('toggle-status', $con_path . '::toggleStatus');
    });
});