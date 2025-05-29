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
});

// Admin Routes Group
$routes->group('admin', ['filter' => 'group:admin'], static function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('manage-withdrawals', 'Admin::manageWithdrawals');
    $routes->get('manage-users', 'Admin::manageUsers');
});