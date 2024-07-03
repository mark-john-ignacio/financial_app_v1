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
$routes->get('/manage-bir', 'ManageBIRFormsController::index');
$routes->post('/manage-bir-forms/show', 'ManageBIRFormsController::show');