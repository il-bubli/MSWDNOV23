<?php
namespace com\icemalta\jobapp\client;

session_start();

use com\icemalta\jobapp\client\controller\RouteController;
use com\icemalta\jobapp\client\helper\ApiHelper;

/** Static Routing --------------------------------------------------------------------------- */
if (PHP_SAPI == 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file))
        return false;
}
/** ------------------------------------------------------------------------------------------ */

require 'vendor/autoload.php';

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;
use \AltoRouter;

$loader = new FilesystemLoader('templates/');
$twig = new Environment($loader, ['debug' => true]);
$twig->addExtension(new \Twig\Extension\DebugExtension());
RouteController::setEnvironment($twig);


$router = new AltoRouter();
/** View Routes ------------------------------------------------------------------------------- */
$router->map('GET', '/', 'RouteController#viewListings', 'view_listings');
$router->map('GET', '/login', 'RouteController#viewLogin', 'view_login');
$router->map('GET', '/register', 'RouteController#viewRegister', 'view_register');
$router->map('GET', '/listing/[i:id]', 'RouteController#viewListing', 'view_listing');
$router->map('GET', '/account', 'RouteController#viewAccount', 'view_account');
$router->map('GET', '/applications', 'RouteController#viewApplications', 'view_applications');
$router->map('GET', '/colour/[a:colour]', 'RouteController#setColourMode', 'view_colour');
/** ------------------------------------------------------------------------------------------ */

/** Action Routes ---------------------------------------------------------------------------- */
$router->map('POST', '/action/login', 'RouteController#actionLogin', 'action_login');
$router->map('GET', '/action/logout', 'RouteController#actionLogout', 'action_logout');
$router->map('POST', '/action/register', 'RouteController#actionRegister', 'action_register');
$router->map('POST', '/action/apply', 'RouteController#actionApply', 'action_apply');
/** ------------------------------------------------------------------------------------------ */

$match = $router->match();
if (is_array($match)) {
	$target = explode('#', $match['target']);
	$class = $target[0];
	$action = $target[1];
	$params = $match['params'];
	$requestData = ApiHelper::getRequestData();
	call_user_func_array(__NAMESPACE__ . "\controller\\$class::$action", array($params, $requestData));
} else {
	header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

