<?php
namespace com\icemalta\jobapp\client\helper;

class ApiHelper
{
    public static function getRequestData(): array
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        switch ($requestMethod) {
            case 'GET':
                return $_GET;
            case 'POST':
                return $_POST;
            default:
                return [];
        }
    }
}