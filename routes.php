<?php

$routes = [
    'GET /' => ['HomeController', 'index'],
    'GET /auth/register' => ['AuthController', 'registerPage'],
    'POST /auth/register' => ['AuthController', 'register'],
    'GET /auth/login' => ['AuthController', 'loginPage'],
    'POST /auth/login' => ['AuthController', 'login'],
    'POST /auth/logout' => ['AuthController', 'logout'],

    // Admin routes
    'GET /admin' => ['AdminController', 'index'],

    // API routes
    'POST /api/v1/auth/login' => ['AuthApiController', 'login'],
];
