<?php

use CodeIgniter\Router\RouteCollection;
use CodeIgniter\Shield\Controllers\ActionController;



/**
 * @var RouteCollection $routes
 */


// Auth Routes
service('auth')->routes($routes, ['except' => ['login', 'register']]);
$routes->get('auth/a/show', [ActionController::class, 'show']);
$routes->post('auth/a/handle', [ActionController::class, 'handle']);
$routes->post('auth/a/verify', [ActionController::class, 'verify']);

$routes->get('login', '\App\Controllers\Auth\LoginController::loginView');
;
$routes->post('login', '\App\Controllers\Auth\LoginController::loginAction');
;
$routes->get('register', '\App\Controllers\Auth\RegisterController::registerView');
$routes->post('register', '\App\Controllers\Auth\RegisterController::registerAction');

// User Routes
$routes->get('/', 'Home::index');
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/profile', 'ProfileController::index');
$routes->get('/claim', 'ClaimController::index');
$routes->post('/claim/action', 'ClaimController::action');
$routes->get('/leaderboard', 'LeaderboardController::index');
$routes->get('/referral', 'ReferralController::index');
$routes->get('/withdraw', 'WithdrawController::index');
$routes->get('claim/getNextClaimTime', 'ClaimController::getNextClaimTime');