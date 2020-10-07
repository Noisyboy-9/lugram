<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->group(['prefix' => '/posts', 'middleware' => 'auth'], function () use ($router) {
    $router->post('', 'Posts\PostsController@store');
    $router->get('', 'Posts\PostsController@index');
});
$router->group(['prefix' => '/auth'], function () use ($router) {
    $router->post('/register', 'Users\RegisterController@store');
    $router->post('/login', 'Users\LoginController@index');
});
