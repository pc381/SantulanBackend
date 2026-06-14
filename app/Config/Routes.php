<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('api/sync', 'SyncController::syncData');
$routes->post('api/sync/file', 'SyncController::uploadFileSync');
