<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../routes.php';
require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../Model/User.php';
// require_once __DIR__ . '/../models/Shop.php';
require_once __DIR__ . '/../controllers/HomeController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
// require_once __DIR__ . '/../controllers/ShopsController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AuthApiController.php';
require_once __DIR__ . '/../middlewares/TokenMiddleware.php';

use Database\Database;
use Model\User;

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
// $shopModel = new Shop($db);

// get the URL path from the request
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove any unwanted characters using a regular expression (allowing only alphanumeric characters, slashes, hyphens, and underscores)
$sanitizedUrl = filter_var($url, FILTER_SANITIZE_URL);
$sanitizedUrl = preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $sanitizedUrl);

// Sanitize request method for safety
$requestMethod = filter_var($_SERVER['REQUEST_METHOD'], FILTER_SANITIZE_STRING);

// Build route key for lookup (e.g., "GET /users")
$routeKey = $requestMethod . ' ' . $url;

$matchedRoute = null;
$matches = [];

// Match the route and find the corresponding controller action
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
        $controller = resolveDependencies($controllerName, $userModel, $shopModel, $_SESSION, $tokenMiddleware);

        // Remove the full match from the matches array
        array_shift($matches);

        // call the method with the matched parameters
        call_user_func_array([$controller, $method], $matches);
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        http_response_code(500);
        include __DIR__ . '/../views/common/500.php';
    }
} else {
    http_response_code(404);
    include __DIR__ . '/../views/common/404.php';
}

/**
 * Resolves and creates a controller instance with dependencies.
 *
 * @param string $controllerName
 * @param User $userModel
//  * @param Shop $shopModel
 * @param array $session
 * @param TokenMiddleware $tokenMiddleware
 * @return object
 * @throws Exception
 */
function resolveDependencies($controllerName, $userModel, $shopModel, &$session, $tokenMiddleware) {
    $dependencies = [
        'HomeController' => [],
        'AdminController' => [],
        // 'ShopsController' => [$shopModel, &$session],
        'AuthController' => [$userModel, &$session],
        'AuthApiController' => [$userModel],
    ];

    if (isset($dependencies[$controllerName])) {
        try {
            $reflection = new ReflectionClass($controllerName);
            return $reflection->newInstanceArgs($dependencies[$controllerName]);
        } catch (Exception $e) {
            throw new Exception("Error instantiating controller: " . $controllerName);
        }
    }

    throw new Exception("Controller not found: " . $controllerName);
}
