<?php
namespace com\icemalta\jobapp\api\controller;

use com\icemalta\jobapp\api\model\AccessToken;

class Controller 
{
    public static function sendResponse(mixed $data = null, int $code = 200, mixed $error = null): void
    {
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        if (!is_null($error)) {
            $response['error'] = [
                'message' => $error,
                'code' => $code
            ];
        }
        http_response_code($code);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public static function checkToken(array $requestData): bool
    {
        if (!isset($requestData['api_user']) || !isset($requestData['api_token'])) {
            return false;
        }
        $token = new AccessToken($requestData['api_user'], $requestData['api_token']);
        return AccessToken::verify($token);
    }
}