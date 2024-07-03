<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/verify_pin', 'PinController::verifyPin');
$routes->post('/set_pin', 'PinController::setPin');
$routes->get('/manage-bir', 'ManageBIRFormsController::index');