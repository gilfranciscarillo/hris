<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

service('auth')->routes($routes);

$routes->group('', ['filter' => 'session'], static function ($routes) {
    $routes->get('/', 'Home::index');
    $routes->group('bank', ['namespace' => 'App\Controllers'], static function ($routes) {
        $routes->get('change_requests/(:num)', 'Bank::changeRequests/$1'); //$1 is the user/employee id

        $routes->get('new_change_request', 'Bank::newChangeRequest');
        $routes->post('create_change_request', 'Bank::createChangeRequest');

        $routes->get('edit_change_request/(:num)', 'Bank::editChangeRequest/$1');
        $routes->patch('update_change_request/(:num)', 'Bank::updateChangeRequest/$1');

        //$1 in the requests below is the request id
        $routes->get('change_responses/(:num)', 'Bank::changeResponses/$1');
        $routes->get('change_response_new/(:num)/(:any)', 'Bank::newChangeResponse/$1/$2'); //$2 is the type of action (approve or reject)
        $routes->post('approve/(:num)', 'Bank::approveChangeRequest/$1');
        $routes->post('reject/(:num)', 'Bank::rejectChangeRequest/$1');

        /**
         * For view, $1 is Encrypted email key from .env plus -{response id} = encrypt({email key}-{response id})
         * Check bank_helper.php emailBankChangeResponse function 
         */     
        $routes->get('view/(:any)', 'Bank::viewChangeResponse/$1');
    });
});
