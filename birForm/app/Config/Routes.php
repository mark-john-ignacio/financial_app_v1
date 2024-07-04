<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/logout-pin', 'PinController::logout');

$routes->post('/verify-pin', 'PinController::verifyPin');
$routes->get('/change-pin', 'PinController::changePin');
$routes->post('/set-pin', 'PinController::setPin');

$routes->get('/bir-year-form/associations', 'BIRYearFormController::associations');

$routes->resource('bir-year-form', ['controller' => 'BIRYearFormController']);