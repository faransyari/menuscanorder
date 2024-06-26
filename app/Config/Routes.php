<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index'); <- Comment out the initial route
$routes->get('/', 'MenuController::index');
$routes->get('/menu', 'MenuController::menu');
$routes->post('/login', 'UserController::login');
$routes->get('/loggedin', 'UserController::loggedin');

$routes->post('/register', 'UserController::register');
$routes->post('/logout', 'UserController::logout');
$routes->get('/admin/logout', 'UserController::logout');

$routes->get('/admin', 'MenuController::admin_index');

$routes->get('/admin/generate_qr', 'TableController::view_generate_qr');
$routes->post('/admin/generate_qr', 'TableController::generateQr');
$routes->get('/admin/generate_qr/delete/(:num)', 'TableController::delete/$1');

$routes->post('/admin/menu_management/add_category', 'MenuController::add_category');
$routes->post('/menu_management/delete_category/(:num)', 'MenuController::delete_category/$1');

$routes->post('/cart/order', 'CartController::order');

$routes->get('/admin/menu_management', 'MenuController::menu_management');
$routes->post('/admin/menu_management/addMenuItem', 'MenuController::addMenuItem');
$routes->post('/admin/menu_management/updateMenuItem', 'MenuController::updateMenuItem');
$routes->get('/menu_management/delete/(:num)', 'MenuController::delete/$1');
$routes->post('/admin/menu_management/toggle_feature', 'MenuController::toggle_feature');
$routes->get('/admin/orders', 'MenuController::orders');
$routes->post('/admin/orders/update_order_status', 'MenuController::update_order_status');



$routes->get('superadmin', 'UserController::manageUsers');
$routes->post('superadmin/updateUserType/(:num)', 'UserController::updateUserType/$1');
$routes->get('superadmin/editAdmin/(:num)', 'UserController::editAdmin/$1');
$routes->post('superadmin/updateAdminDetails/(:num)', 'UserController::updateAdminDetails/$1');
$routes->get('superadmin/activateUser/(:num)', 'UserController::activateUser/$1');
$routes->get('superadmin/deactivateUser/(:num)', 'UserController::deactivateUser/$1');
$routes->get('superadmin/deleteUser/(:num)', 'UserController::deleteUser/$1');


service('auth')->routes($routes);

 





