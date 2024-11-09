<?php

$routes = [
    'GET /auth/register' => ['AuthController', 'registerPage'],
    'POST /auth/register' => ['AuthController', 'register'],
    'GET /auth/login' => ['AuthController', 'loginPage'],
    'POST /auth/login' => ['AuthController', 'login'],
    'POST /auth/logout' => ['AuthController', 'logout'],

    // API routes
    'POST /api/v1/auth/login' => ['AuthApiController', 'login'],
];
