<?php
namespace com\icemalta\jobapp\client\controller;

use com\icemalta\jobapp\client\controller\{
    Controller,
    ListingController,
    UserController
};
use \Twig\Environment;

class RouteController extends Controller
{
    private static ?Environment $twig = null;
    private static string $colour = 'light';
    private static string $currentView = 'Listings';

    public static function showView(string $view, ?array $params = []): void
    {
        self::$currentView = ucfirst($view);
        self::$colour = $_SESSION['colour'] ?? self::$colour;
        $params['currentView'] = self::$currentView;
        $params['colour'] = self::$colour;
        echo self::$twig->render("$view.twig", $params);

    }

    private static function authorise(?callable $fn = null, ?string $view = null, ?array $params = [], ?array $data = []): void
    {
        $loggedIn = Controller::checkToken();
        if ($loggedIn) {
            $params['loggedIn'] = true;
            if (is_callable($fn)) {
                $fn($params, $data);
            } else if (isset($view)) {
                call_user_func_array(__NAMESPACE__ . "\\RouteController::view$view", array($params, $data));
            }
        } else {
            $params['redirect'] = $view;
            self::showView('login', $params);
        }
    }

    public static function setEnvironment(Environment $twig): void
    {
        self::$twig = $twig;
    }

    public static function viewLogin(array $params, array $data): void
    {
        self::showView('login', $params);
    }

    public static function viewRegister(array $params, array $data): void
    {
        self::showView('register', $params);
    }

    public static function viewListings(array $params, array $data): void
    {
        self::authorise(fn: function ($params, $data) {
            $params['listings'] = ListingController::getListings($params, $data)->data;
            self::showView('listings', $params);
        }, view: 'Listings', params: $params, data: $data);
    }

    public static function viewListing(array $params, array $data): void
    {
        self::authorise(fn: function ($params, $data) {
            $params['listing'] = ListingController::getListing($params, $data)->data;
            self::showView('listing', $params);
        }, view: 'Listing', params: $params, data: $data);
    }

    public static function viewAccount(array $params, array $data): void
    {
        self::authorise(fn: function ($params, $data) {
            $params['account'] = UserController::getInfo($params, $data)->data;
            self::showView('account', $params);
        }, view: 'Account', params: $params, data: $data);
    }

    public static function viewApplications(array $params, array $data): void
    {
        self::authorise(fn: function ($params, $data) {
            $params['applications'] = UserController::getApplications($params, $data)->data;
            self::showView('applications', $params);
        }, view: 'Applications', params: $params, data: $data);
    }

    public static function actionLogin(array $params, array $data): void
    {
        $result = AuthController::login($params, $data);
        if (isset($result->data)) {
            $_SESSION['api_user'] = $result->data->user;
            $_SESSION['api_token'] = $result->data->token;
            self::authorise(view: empty($data['redirect']) ? 'listings' : $data['redirect']);
        } else {
            self::showView('login', ['error' => 'Login Failed. Please try again.']);
        }
    }

    public static function actionRegister(array $params, array $data): void
    {
        $result = UserController::register($params, $data);
        if (isset($result->data)) {
            self::showView('login', ['success' => 'Registration succeeded! You can now login.']);
        } else {
            self::showView('register', ['error' => 'Registration Failed. Please try again.']);
        }
    }

    public static function actionLogout(array $params, array $data): void
    {
        AuthController::logout();
        self::showView('login', $params);
    }

    public static function actionApply(array $params, array $data): void
    {
        $result = UserController::apply($params, $data);
        if (isset($result->data)) {
            $params['success'] = 'Your application has been registered!';
        } else {
            $params['error'] = 'Your application has failed. Please try again.';
        }
        self::viewApplications($params, $data);

    }

    public static function setColourMode(array $params, array $data): void
    {
        $_SESSION['colour'] = $params['colour'];
        self::authorise(view: self::$currentView, params: $params, data: $data);
    }
}