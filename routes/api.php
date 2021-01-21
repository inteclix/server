<?php

$router->get('/', function () {
    return response()->json([
        'api' => 'cl-server-api',
        'description' => 'gestPark server api'
    ]);
});

$router->post('auth/login', [
    'uses' => 'AuthController@authenticate',
    'as' => 'api.auth.login'
]);


$router->get('/state/test', [
    'uses' => 'StateController@test',
    'as' => 'api.state.test'
]);

$router->group(['middleware' => 'jwt.auth'], function () use ($router) {
    ///////////// auth methods

    $router->get('/auth/users/cars/{id}', [
        'uses' => 'AuthController@getCars',
        'as' => 'api.auth.getCars'
    ]);
    $router->post('/auth/users/cars', [
        'uses' => 'AuthController@setCars',
        'as' => 'api.auth.setCars'
    ]);

    $router->get('/auth/users/search', [
        'uses' => 'AuthController@search',
        'as' => 'api.auth.search'
    ]);
    $router->get('/auth/me', [
        'uses' => 'AuthController@me',
        'as' => 'api.auth.me'
    ]);

    $router->get('/auth/users/{id}', [
        'uses' => 'AuthController@getUser',
        'as' => 'api.auth.get'
    ]);

    $router->get('/auth/users', [
        'uses' => 'AuthController@getUsers',
        'as' => 'api.auth.getAll'
    ]);

    $router->post('/auth/users', [
        'uses' => 'AuthController@createUser',
        'as' => 'api.auth.create'
    ]);

    $router->post('/auth/users/delete/{id}', [
        'uses' => 'AuthController@delete',
        'as' => 'api.auth.delete'
    ]);

    $router->put('/auth/users/{id}', [
        'uses' => 'AuthController@update',
        'as' => 'api.auth.update'
    ]);

    $router->post('/auth/users/roles', [
        'uses' => 'AuthController@setUserRoles',
        'as' => 'api.auth.setUserRoles'
    ]);

    $router->get('/auth/users/{id}/roles', [
        'uses' => 'AuthController@getUserRoles',
        'as' => 'api.auth.getUserRoles'
    ]);

    $router->post('/notifications/{id}', [
        'uses' => 'AuthController@makeNotificationAsRead',
        'as' => 'api.notifications.update'
    ]);
    $router->get('/notifications', [
        'uses' => 'AuthController@getNotifications',
        'as' => 'api.notifications.get'
    ]);

    $router->get('/states', [
        'uses' => 'AuthController@getStates',
        'as' => 'api.States.get'
    ]);

    // roles

    $router->get('/roles/{id}', [
        'uses' => 'RoleController@get',
        'as' => 'api.roles.get'
    ]);

    $router->get('/roles', [
        'uses' => 'RoleController@getAll',
        'as' => 'api.roles.getAll'
    ]);

    $router->post('/roles', [
        'uses' => 'RoleController@create',
        'as' => 'api.roles.create'
    ]);

    $router->post('/roles/delete/{id}', [
        'uses' => 'RoleController@delete',
        'as' => 'api.roles.delete'
    ]);

    $router->put('/roles/{id}', [
        'uses' => 'RoleController@update',
        'as' => 'api.roles.update'
    ]);

    // cars
    $router->get('/cars/decharges/{car_id}', [
        'uses' => 'CarController@getDechargeHistory',
        'as' => 'api.cars.getDechargeHistory'
    ]);
    $router->get('/cars/search', [
        'uses' => 'CarController@search',
        'as' => 'api.cars.search'
    ]);

    $router->get('/cars/export', [
        'uses' => 'CarController@export',
        'as' => 'api.cars.export'
    ]);

    $router->post('/cars/import', [
        'uses' => 'CarController@import',
        'as' => 'api.cars.import'
    ]);

    $router->get('/cars/clients/{id}', [
        'uses' => 'CarController@getClients',
        'as' => 'api.cars.getClients'
    ]);

    $router->get('/cars/drivers/{id}', [
        'uses' => 'CarController@getDrivers',
        'as' => 'api.cars.getDrivers'
    ]);

    $router->get('/cars/{id}', [
        'uses' => 'CarController@get',
        'as' => 'api.cars.get'
    ]);

    $router->get('/cars', [
        'uses' => 'CarController@getAll',
        'as' => 'api.cars.getAll'
    ]);
    $router->get('/allcars', [
        'uses' => 'CarController@getAllCars',
        'as' => 'api.cars.getAllCars'
    ]);
    $router->post('/cars', [
        'uses' => 'CarController@create',
        'as' => 'api.cars.create'
    ]);

    $router->post('/cars/delete/{id}', [
        'uses' => 'CarController@delete',
        'as' => 'api.cars.delete'
    ]);

    $router->put('/cars/{id}', [
        'uses' => 'CarController@update',
        'as' => 'api.cars.update'
    ]);

    // clients
    $router->post('/clients/import', [
        'uses' => 'ClientController@import',
        'as' => 'api.clients.import'
    ]);

    $router->get('/clients/search', [
        'uses' => 'ClientController@search',
        'as' => 'api.clients.search'
    ]);
    $router->get('/clients/{id}', [
        'uses' => 'ClientController@get',
        'as' => 'api.clients.get'
    ]);

    $router->get('/clients', [
        'uses' => 'ClientController@getAll',
        'as' => 'api.clients.getAll'
    ]);

    $router->post('/clients', [
        'uses' => 'ClientController@create',
        'as' => 'api.clients.create'
    ]);

    $router->post('/clients/delete/{id}', [
        'uses' => 'ClientController@delete',
        'as' => 'api.clients.delete'
    ]);

    $router->put('/clients/{id}', [
        'uses' => 'ClientController@update',
        'as' => 'api.clients.update'
    ]);

    // drivers

    $router->get('/drivers/export', [
        'uses' => 'DriverController@export',
        'as' => 'api.drivers.export'
    ]);

    $router->post('/drivers/import', [
        'uses' => 'DriverController@import',
        'as' => 'api.drivers.import'
    ]);

    $router->get('/drivers/search', [
        'uses' => 'DriverController@search',
        'as' => 'api.drivers.search'
    ]);

    $router->get('/drivers/{id}', [
        'uses' => 'DriverController@get',
        'as' => 'api.drivers.get'
    ]);

    $router->get('/drivers', [
        'uses' => 'DriverController@getAll',
        'as' => 'api.drivers.getAll'
    ]);

    $router->post('/drivers', [
        'uses' => 'DriverController@create',
        'as' => 'api.drivers.create'
    ]);

    $router->post('/drivers/delete/{id}', [
        'uses' => 'DriverController@delete',
        'as' => 'api.drivers.delete'
    ]);

    $router->put('/drivers/{id}', [
        'uses' => 'DriverController@update',
        'as' => 'api.drivers.update'
    ]);

    // affectations

    $router->post('/affectations/restitition/{id}', [
        'uses' => 'CarClientController@restitition',
        'as' => 'api.car.client.restitition'
    ]);



    $router->get('/affectations', [
        'uses' => 'CarClientController@getAffectations',
        'as' => 'api.car.client.getAffectations'
    ]);

    $router->get('/affectations/{id}', [
        'uses' => 'CarClientController@get',
        'as' => 'api.car.client.getAffectation'
    ]);

    $router->post('/affectations/', [
        'uses' => 'CarClientController@create',
        'as' => 'api.car.client.create'
    ]);
    $router->post('/affectations/delete/{id}', [
        'uses' => 'CarClientController@delete',
        'as' => 'api.car.client.delete'
    ]);
    $router->put('/affectations/{id}', [
        'uses' => 'CarClientController@update',
        'as' => 'api.car.client.update'
    ]);

    // decharges drivers

    $router->post('/decharges/restitition/{id}', [
        'uses' => 'DechargeController@restitition',
        'as' => 'api.decharges.restitition'
    ]);

    $router->post('/decharges/checklist/{id}', [
        'uses' => 'DechargeController@addChecklist',
        'as' => 'api.decharges.addChecklist'
    ]);

    $router->post('/decharges/checklist/delete/{id}', [
        'uses' => 'DechargeController@deleteChecklist',
        'as' => 'api.decharges.deleteChecklist'
    ]);



    $router->get('/decharges', [
        'uses' => 'DechargeController@getDecharges',
        'as' => 'api.decharges.getDecharges'
    ]);

    $router->get('/decharges/restititions/', [
        'uses' => 'DechargeController@getDechargesRestititions',
        'as' => 'api.decharges.getDechargesRestititions'
    ]);

    $router->post('/decharges/restititions/delete/{id}', [
        'uses' => 'DechargeController@deleteRestitition',
        'as' => 'api.decharges.deleteRestitition'
    ]);
    $router->get('/decharges/{id}', [
        'uses' => 'DechargeController@get',
        'as' => 'api.decharges.get'
    ]);


    $router->post('/decharges/delete/{id}', [
        'uses' => 'DechargeController@deleteDecharge',
        'as' => 'api.decharges.deleteDecharge'
    ]);

    $router->post('/decharges/', [
        'uses' => 'DechargeController@createDecharge',
        'as' => 'api.decharge.create'
    ]);

    
    // groups cars

    $router->get('/groups/search', [
        'uses' => 'GroupController@search',
        'as' => 'api.groups.search'
    ]);

    $router->get('/groups/cars/{id}', [
        'uses' => 'GroupController@getCars',
        'as' => 'api.groups.getCars'
    ]);

    $router->post('/groups/cars/{id}', [
        'uses' => 'GroupController@setCars',
        'as' => 'api.groups.setCars'
    ]);
    // cars states

    $router->get('/cars_state/search', [
        'uses' => 'CarStateController@search',
        'as' => 'api.cars_state.search'
    ]);

    $router->get('/cars_state/{id}', [
        'uses' => 'CarStateController@get',
        'as' => 'api.cars_state.get'
    ]);

    $router->get('/cars_state', [
        'uses' => 'CarStateController@getAll',
        'as' => 'api.cars_state.getAll'
    ]);

    $router->post('/cars_state', [
        'uses' => 'CarStateController@create',
        'as' => 'api.cars_state.create'
    ]);

    $router->post('/cars_state/delete/{id}', [
        'uses' => 'CarStateController@delete',
        'as' => 'api.cars_state.delete'
    ]);

    $router->put('/cars_state/{id}', [
        'uses' => 'CarStateController@update',
        'as' => 'api.cars_state.update'
    ]);

    // exa

    $router->get('/state/numbers_roles_users', [
        'uses' => 'StateController@getNumbersrolesAndUsers',
        'as' => 'api.state.getNumbersrolesAndUsers'
    ]);

    $router->get('/state/roles_excel', [
        'uses' => 'StateController@dowloadroles',
        'as' => 'api.state.dowloadroles'
    ]);
});
