<?php
namespace com\icemalta\jobapp\api\controller;

use com\icemalta\jobapp\api\model\{User, AccessToken};

class AuthController extends Controller
{
    public static function login(array $params, array $data): void
    {
        $email = $data['email'];
        $password = $data['password'];
        $user = new User(email: $email, password: $password);
        $user = User::authenticate($user);
        if ($user) {
            $token = new AccessToken(userId: $user->getId());
            $token = AccessToken::save($token);
            self::sendResponse(data: ['user' => $user->getId(), 'token' => $token->getToken()]);
        } else {
            self::sendResponse(code: 401, error: 'Login failed.');
        }
    }

    public static function logout(array $params, array $data): void
    {
        if (self::checkToken($data)) {
            $userId = $data['api_user'];
            $token = new AccessToken(userId: $userId);
            $token = AccessToken::delete($token);
            self::sendResponse(data: ['message' => 'You have been logged out.']);
            } else {
            self::sendResponse(code: 403, error: 'Missing, invalid or expired token.');
            }
    }

    public static function verifyToken(array $params, array $data): void
    {
        if (self::checkToken($data)) {
            self::sendResponse(data: ['valid' => true, 'token' => $data['api_token']]);
            } else {
            self::sendResponse(data: ['valid' => false, 'token' => $data['api_token']]);
            }
    }

    public static function connectionTest(array $params, array $data): void
    {
        self::sendResponse(data: 'Welcome to the JobApp API!');
    }
}