<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => '/posts', 'middleware' => 'auth'], function () use ($router) {
    $router->post('', 'Posts\PostsController@store');
    $router->get('', 'Posts\PostsController@index');
    $router->delete('/{id}', 'Posts\PostsController@destroy');
});
$router->group(['prefix' => '/auth'], function () use ($router) {
    $router->post('/register', 'Users\RegisterController@store');
    $router->post('/login', 'Users\LoginController@index');
});


$router->post('/requests/{userId}', [
    'middleware' => 'auth',
    'uses' => 'Follows\FollowRequestsController@store',
]);
$router->put('/requests/{userId}/accept', [
    'middleware' => 'auth',
    'uses' => 'Follows\AcceptFollowRequestsController@update',
]);

$router->put('/requests/{userId}/decline', [
    'middleware' => 'auth',
    'uses' => 'Follows\DeclineFollowRequestsController@update',
]);
