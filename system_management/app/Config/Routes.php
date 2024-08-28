<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->post('/verify-pin', 'PinController::verifyPin');

$routes->post('/receive-order', 'WooCommerceOrderSync\OrderController::receiveOrder');

$routes->group('', ['filter' => 'pin_verified'], function ($routes) {

    $routes->get('/change-pin', 'PinController::changePin');
    $routes->post('/set-pin', 'PinController::setPin');
    $routes->get('/logout-pin', 'PinController::logout');

    $routes->group('bir-forms', function ($routes) {
        $con_path = 'BIRForms\\BIRYearFormController';
        $routes->get('year-form/associations', $con_path . '::associations');
        $routes->resource('year-form', ['controller' => $con_path]);
        $routes->post('year-form/new', $con_path . '::new');

        $con_path = 'BIRForms\\BIRFormController';
        $routes->get('form/load', $con_path . '::load');
        $routes->resource('form', ['controller' => $con_path]);
    });
    
    $routes->group('bir-forms-image', function ($routes) {
        $con_path = 'BIRForms\\BIRFormImage';
        $routes->get('form/(:num)/edit', $con_path . '::new/$1', ['as' => 'form-image-edit']);
        $routes->post('form/(:num)/create', $con_path . '::create/$1');
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

    $routes->group('company-switcher', function ($routes) {
        $con_path = 'CompanySwitcher\\CompanySwitcherController';
        $routes->get('switch-company/(:segment)', $con_path . '::switchCompany/$1');
    });


    $routes->group('', function($routes) {
        $con_path = 'Testing\\Upload';
        $routes->get('upload', $con_path . '::index');
        $routes->post('upload/upload', $con_path . '::upload');
    });

    //TODO: Remove this route
    $routes->group('testing', function ($routes) {
        $con_path = 'Testing\\AddId';
        $routes->get('add-id-to-so-table', $con_path . '::AddIdToSOTable');
        $routes->get('remove-id-from-so-table', $con_path . '::RemoveIdFromSOTable');
        $routes->get('add-id-to-so-t-table', $con_path . '::AddIdToSOTTableMigration');
        $routes->get('remove-id-from-so-t-table', $con_path . '::RemoveIdFromSOTTableMigration');
        $routes->get('run-migration', $con_path . '::runMigration');
        $routes->get('rollback-migration', $con_path . '::rollbackMigration');
    });

    $routes->group('customers', function ($routes) {
        $con_path = 'Customers\\Customers';
        $routes->resource('', ['controller' => $con_path]);
        $routes->get('load', $con_path . '::load');
        $routes->get('upload_form', $con_path . '::upload_form', ['as' => 'customers-upload-form']);
        $routes->post('upload', $con_path . '::upload');
        $routes->post('insert-customers', $con_path . '::insertCustomers');
        $routes->get('download-template', $con_path . '::downloadTemplate');
    });

    $routes->group('items', function ($routes) {
        $con_path = 'Items\\ItemsController';
        $routes->resource('', ['controller' => $con_path]);
        $routes->get('load', $con_path . '::load', ['as' => 'items-load']);
        $routes->get('upload_form', $con_path . '::upload_form', ['as' => 'items-upload-form']);
        $routes->post('upload', $con_path . '::upload', ['as' => 'items-upload']);
        $routes->post('insert-items', $con_path . '::insertItems', ['as' => 'items-insert-items']);
        $routes->get('download-template', $con_path . '::downloadTemplate', ['as' => 'items-download-template']);
        //$routes->get('delete-all', $con_path . '::deleteAll');
    });

    $routes->group('suppliers', function ($routes) {
        $con_path = 'Suppliers\\SuppliersController';
        $routes->resource('', ['controller' => $con_path]);
        $routes->get('load', $con_path . '::load', ['as' => 'suppliers-load']);
        $routes->get('upload_form', $con_path . '::upload_form', ['as' => 'suppliers-upload-form']);
        $routes->post('upload', $con_path . '::upload', ['as' => 'suppliers-upload']);
        $routes->post('insert-suppliers', $con_path . '::insertSuppliers', ['as' => 'suppliers-insert-suppliers']);
        $routes->get('download-template', $con_path . '::downloadTemplate', ['as' => 'suppliers-download-template']);
        //$routes->get('delete-all', $con_path . '::deleteAll');
    });

    $routes->group('item-code-sync', function ($routes) {
        $con_path = 'ItemCodeSync\\ItemCodeSyncController';
        $routes->get('', $con_path . '::index');
        $routes->get('load', $con_path . '::load');
        $routes->get('item-mapping', $con_path . '::mapItemCodes', ['as' => 'item-mapping']);
        $routes->post('replace-item-codes', $con_path . '::replaceItemCodes', ['as' => 'replace-item-codes']);
    });

    $routes->group('order-sync', function($routes){
        $con_path = 'WooCommerceOrderSync\\OrderController';
        $routes->get('', $con_path . '::index');
        $routes->get('load', $con_path . '::getPendingOrders', ['as' => 'order-sync-load']);
        $routes->get('edit/(:num)', $con_path . '::edit/$1', ['as' => 'order-sync-edit']);
        $routes->get('load-order/(:num)', $con_path . '::loadOrder/$1', ['as' => 'order-sync-load-order']);
    });
});


$routes->group('api', ['namespace' => 'App\Controllers\API\BIRForms'], function($routes) {
    $routes->get('birforms/getCompanyInfo', 'BirFormsApi::getCompanyInfo');
    $routes->get('birforms/getApvData', 'BirFormsApi::getApvData');
    $routes->get('birforms/getDefaultAccounts', 'BirFormsApi::getDefaultAccounts');
});

$routes->get('bir-forms/1601eq', 'BirForms::form1601eq');
$routes->post('bir-forms/submit-1601eq', 'BirForms::submit1601eq');

$routes->post('api/pdf/0619e', 'API\\BIRPDF\\BIRPDF0619E::generatePdf');

$routes->post('api/pdf2550q', 'API\\BIRPDF\\BIRPDF2550Q::generatePdf');
$routes->post('api/pdfs', 'API\\BIRPDF\\BIRPDF2550Q::generatePdf');

$routes->post('api/pdf/2550m', 'API\\BIRPDF\\BIRPDF2550M::generatePdf');
// $routes->get('api/pdf/2550m', 'API\\BIRPDF\\BIRPDF2550M::generatePdf');
