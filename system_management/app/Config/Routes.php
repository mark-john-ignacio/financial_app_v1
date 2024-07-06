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

    $routes->get('/bir-year-form/associations', 'BIRYearFormController::associations');

    $routes->resource('bir-year-form', ['controller' => 'BIRYearFormController']);
    $routes->post('/bir-year-form/new', 'BIRYearFormController::new');
});