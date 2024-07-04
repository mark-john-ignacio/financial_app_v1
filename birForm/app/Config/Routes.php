<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/logout', 'PinController::logout');
$routes->post('/verify_pin', 'PinController::verifyPin');
$routes->get('/change-pin', 'PinController::changePin');
$routes->post('/set_pin', 'PinController::setPin');
$routes->get('/manage-bir', 'BIRYearFormController::index');
$routes->get('/manage-bir-forms/associations', 'BIRYearFormController::associations');

$routes->resource('bir-year-form', ['controller' => 'BIRYearFormController']);