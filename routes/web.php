<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->post('/posts', 'PostsController@store');

// users
$router->post('/login', 'UsersController@login');
