<?php
namespace com\icemalta\jobapp\client\controller;

use com\icemalta\jobapp\client\controller\Controller;

class AuthController extends Controller
{
    public static function login($params, $data): object
    {
        $payload = [
            'email' => $data['email'],
            'password' => $data['password']
        ];
        return json_decode(self::req('POST', '/login', $payload));
    }

    public static function logout(): void
    {
        $_SESSION['api_token'] = null;
        $_SESSION['api_user'] = null;
        session_destroy();
    }
}