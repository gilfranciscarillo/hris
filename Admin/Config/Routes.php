<?php

namespace Admin\Config;

use Config\Services;

$routes = Services::routes();
$routes->group('admin', ['namespace' => 'Admin\Controllers', 'filter' => 'session'], static function ($routes) {
    $routes->resource('users', ['placeholder' => '(:num)']);
    $routes->patch('users/add_to_group/(:num)/(:any)', 'Users::addToGroup/$1/$2');
    $routes->patch('users/remove_from_group/(:num)/(:any)', 'Users::removeFromGroup/$1/$2');
});
