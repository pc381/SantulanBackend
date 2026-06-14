<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('api/sync', 'SyncController::syncData');
$routes->post('api/sync/file', 'SyncController::uploadFileSync');

// Auth API routes
$routes->post('api/auth/register', 'AuthController::register');
$routes->get('api/auth/verify', 'AuthController::verify');
$routes->post('api/auth/login', 'AuthController::login');
$routes->post('api/auth/google', 'AuthController::google');
$routes->post('api/auth/forgot-password', 'AuthController::forgotPassword');
$routes->post('api/auth/reset-password', 'AuthController::resetPassword');

// Admin panel web routes
$routes->get('admin', 'AdminController::login');
$routes->get('admin/login', 'AdminController::login');
$routes->post('admin/login', 'AdminController::processLogin');
$routes->get('admin/dashboard', 'AdminController::dashboard');
$routes->post('admin/approve/(:num)', 'AdminController::approve/$1');
$routes->post('admin/reject/(:num)', 'AdminController::reject/$1');
$routes->get('admin/logout', 'AdminController::logout');
