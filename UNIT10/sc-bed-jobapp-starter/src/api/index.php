<?php
namespace com\icemalta\jobapp\api;

use \AltoRouter;
use com\icemalta\jobapp\api\helper\ApiHelper;

require 'vendor/autoload.php';

/** BASIC SETTING -----------------*/
$BASE_URI = '/jobapp/api';
header("Content-Type: application/json; charset=UTF-8");
ApiHelper::handleCors();
/**------------------------------- */

$router = new AltoRouter();
$router->setBasePath($BASE_URI);

/** Test Route ------------------- */
$router->map('GET', '/', 'AuthController#connectionTest', 'test');
/**------------------------------- */

/** User Management Routes ------- */
$router->map('POST', '/user', 'UserController#register', 'user_register');
$router->map('GET', '/user', 'UserController#getInfo', 'user_info');
$router->map('GET', '/user/applications', 'UserController#getApplications', 'user_get_applications');
/** ------------------------------ */

/** Authentication Routes ----------------------------------------------------------- */
$router->map('POST', '/login', 'AuthController#login', 'auth_login');
$router->map('POST', '/logout', 'AuthController#logout', 'auth_logout');
$router->map('GET', '/token', 'AuthController#verifyToken', 'auth_verify_token');
/**----------------------------------------------------------------------------------- */

/** Listing Routes ------------------------------------------------------------------ */
$router->map('GET', '/listing', 'ListingController#getAll', 'listing_get_all');
$router->map('GET', '/listing/[i:id]', 'ListingController#get', 'listing_get');
/** --------------------------------------------------------------*/

/** Application Routes -------------------------------------------------------------- */
$router->map('GET', '/application', 'ApplicationController#getAll', 'application_get_all');
$router->map('GET', '/application/[i:id]', 'ApplicationController#get', 'application_get');
$router->map('POST', '/application', 'ApplicationController#apply', 'application_apply');
/** --------------------------------------------------------------------------------- */

$match = $router->match();
if (is_array($match)) {
    $target = explode('#', $match['target']);
    $class = $target[0];
    $action = $target[1];
    $params = $match['params'];
    $requestData = ApiHelper::getRequestData();
    if (isset($_SERVER["HTTP_X_API_KEY"])) {
        $requestData["api_user"] = $_SERVER["HTTP_X_API_USER"];
    }
    if (isset($_SERVER["HTTP_X_API_KEY"])) {
        $requestData["api_token"] = $_SERVER["HTTP_X_API_KEY"];
    }
    call_user_func_array(__NAMESPACE__ . "\controller\\$class::$action", array($params, $requestData));
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}