<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => '/posts', 'middleware' => 'auth'], function () use ($router) {
    $router->post('', 'Posts\PostsController@store');
    $router->get('', 'Posts\PostsController@index');
    $router->delete('/{id}', 'Posts\PostsController@destroy');
});
$router->group(['prefix' => '/auth'], function () use ($router) {
    $router->post('/register', 'Users\UserAccountsController@store');
    $router->post('/login', 'Users\LoginController@index');
});
$router->group(['prefix' => '/requests/{userId}', 'middleware' => 'auth'], function () use ($router) {
    $router->post('', 'Follows\FollowRequestsController@store');
    $router->put('/accept', 'Follows\AcceptFollowRequestsController@update');
    $router->put('decline', 'Follows\DeclineFollowRequestsController@update');
});
