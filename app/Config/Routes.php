<?php

use CodeIgniter\Router\RouteCollection;
use CodeIgniter\Shield\Controllers\ActionController;
use CodeIgniter\Shield\Controllers\LoginController;



/**
 * @var RouteCollection $routes
 */


// Auth Routes
service('auth')->routes($routes, ['except' => ['login', 'register']]);
$routes->get('auth/a/show', [ActionController::class, 'show']);
$routes->post('auth/a/handle', [ActionController::class, 'handle']);
$routes->post('auth/a/verify', [ActionController::class, 'verify']);

$routes->get('login', [LoginController::class, 'loginView']);
;
$routes->post('login', [LoginController::class, 'loginAction']);
;
$routes->get('register', '\App\Controllers\Auth\RegisterController::registerView');
$routes->post('register', '\App\Controllers\Auth\RegisterController::registerAction');
$routes->get('captcha-request', 'Captcha::request');
$routes->post('captcha-request', 'Captcha::request');

// Public Routes (no authentication required)
$routes->get('/', 'Home::index');
// User Routes Group
$routes->group('', ['filter' => 'group:user'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('profile', 'Profile::index');
    $routes->get('challenge', 'Challenge::index');
    $routes->post('challenge/claim/(:num)', 'Challenge::claim/$1');
    $routes->get('claim', 'Claim::index');
    $routes->post('claim/action', 'Claim::store');
    $routes->get('claim/status', 'Claim::show');
    $routes->get('referral', 'Referral::index');
    $routes->get('withdrawal', 'Withdrawal::index');
    $routes->post('withdrawal/send', 'Withdrawal::sendPayment');
});

// Admin Routes Group
$routes->group('admin', ['filter' => 'group:admin'], static function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('profile', 'Admin::profile');
    $routes->get('manage-withdrawals', 'Admin::manageWithdrawals');
    $routes->get('manage-users', 'Admin::manageUsers');
    $routes->get('manage-users/edit/(:num)', 'Admin::editUser/$1');

    $routes->post('manage-users/update/(:num)', 'Admin::updateUser/$1');
    $routes->get('manage-users/ban/(:num)', 'Admin::banUser/$1');

    $routes->get('manage-users/unban/(:num)', 'Admin::unbanUser/$1');

});