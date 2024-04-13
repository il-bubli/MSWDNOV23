<?php
namespace com\icemalta\jobapp\client\controller;

use com\icemalta\jobapp\client\controller\{Controller, ListingController};
use \stdClass;

class UserController extends Controller
{
    public static function getInfo(array $params, array $data): object
    {
        $userInfo = json_decode(self::req('GET', "/user", $data));
        return $userInfo;
    }

    public static function register(array $params, array $data): object
    {
        $payload = [
            'email' => $data['email'],
            'password' => $data['password']
        ];
        return json_decode(self::req('POST', '/user', $payload));
    }

    public static function getApplications(array $params, array $data): object
    {
        $applications = json_decode(self::req('GET', '/user/applications', $data), false)->data;
        foreach ($applications as &$app) {
            $listing = ListingController::getListing(['id' => $app->listingId], $data)->data;
            $app->listing = $listing;
        }
        $result = new stdClass();
        $result->data = $applications;
        return $result;
    }

    public static function apply(array $params, array $data): object
    {
        $payload = [
            'listingId' => $data['listingId'],
        ];
        return json_decode(self::req('POST', '/application', $payload));
    }
}