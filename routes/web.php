<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->post('/posts', [
    'middleware' => 'auth',
    'uses' => 'Posts\PostsController@store',
]);

$router->group(['prefix' => '/auth'], function () use ($router) {
    $router->post('/register', 'Users\RegisterController@store');
    $router->post('/login', 'Users\LoginController@index');
});
