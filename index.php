<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'routes.php';
require_once 'config/Database.php';
require_once 'models/User.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/AuthApiController.php';
require_once 'middlewares/TokenMiddleware.php';

// start the session
session_start();
// prevent session fixation attacks
session_regenerate_id(true);

// initialize the database
$database = Database::getInstance();
$db = $database->getConnection();

// Initialize the middlewares
$tokenMiddleware = new TokenMiddleware();

// Initialize the models
$userModel = new User($db);

// get the URL path from the request
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove any unwanted characters using a regular expression (allowing only alphanumeric characters, slashes, hyphens, and underscores)
$sanitizedUrl = filter_var($url, FILTER_SANITIZE_URL);
$sanitizedUrl = preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $sanitizedUrl);

// Sanitize request method for safety
$requestMethod = filter_var($_SERVER['REQUEST_METHOD'], FILTER_SANITIZE_STRING);

// Build route key for lookup (e.g., "GET /users")
$routeKey = $requestMethod . ' ' . $url;

// check URL matches any defined route pattern
foreach ($routes as $routePattern => $controllerAction) {
    if (preg_match("#^$routePattern$#", $routeKey, $matches)) {
        $matchedRoute = $controllerAction;
        break;
    }
}

if ($matchedRoute) {
    [$controllerName, $method] = $matchedRoute;
    
    // create the controller instance with dependencies
    try {
        $controller = resolveDependencies($controllerName, $userModel, $_SESSION, $tokenMiddleware);

        // remove the first element, which is the full match
        array_shift($matches);

        // call the method with the matched parameters
        call_user_func_array([$controller, $method], $matches);
    } catch (Exception $e) {
        http_response_code(500);
        include __DIR__ . '/views/common/500.php';
    }
} else {
    http_response_code(404);
    include __DIR__ . '/views/common/404.php';
}

function resolveDependencies($controllerName, $userModel, &$session, $tokenMiddleware) {
    $dependencies = [
        'HomeController' => [],
        'AdminController' => [],
        'AuthController' => [$userModel, &$session],
        'AuthApiController' => [$userModel],
    ];

    if (isset($dependencies[$controllerName])) {
        $reflection = new ReflectionClass($controllerName);
        return $reflection->newInstanceArgs($dependencies[$controllerName]);
    }

    throw new Exception("Controller not found: " . $controllerName);
}
