<?php
namespace com\icemalta\jobapp\api\controller;

use com\icemalta\jobapp\api\model\User;

class UserController extends Controller
{
    public static function register(array $params, array $data): void
    {
        $email = $data['email'];
        $password = $data['password'];
        $user = new User(email: $email, password: $password);
        $user = User::save($user);
        self::sendResponse(data: $user, code: 201);
    }

    public static function getApplications(array $params, array $data): void
    {
        if (self::checkToken($data)) {
            $userId = $data['api_user'];
            $user = new User(id: $userId);
            $applications = User::getApplications($user);
            self::sendResponse(data: $applications);
            } else {
            self::sendResponse(code: 403, error: 'Missing, invalid or expired token.');
            }
    }

    public static function getInfo(array $params, array $data): void
    {
        if (self::checkToken($data)) {
            $userId = $data['api_user'];
            $user = new User(id: $userId);
            $userInfo = User::getInfo($user);
            self::sendResponse(data: $userInfo);
            } else {
            self::sendResponse(code: 403, error: 'Missing, invalid or expired token.');
            }
    }
}