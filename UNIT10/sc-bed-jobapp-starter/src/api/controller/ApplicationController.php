<?php
namespace com\icemalta\jobapp\api\controller;

use com\icemalta\jobapp\api\model\{Application, User, Listing};

class ApplicationController extends Controller
{

    public static function getAll(array $params, array $data): void
    {
        if (
            self::checkToken
            ($data)
        ) {
            $applications = Application::getAll();
            self::sendResponse
            (data: $applications);
        } else {
            self::sendResponse
            (code: 403, error: 'Missing, invalid or expired token.');
        }
    }

    public static function get(array $params, array $data): void
    {
        if (
            self::checkToken
            ($data)
        ) {
            $application = new Application(id: $params
            ['id']);
            $applications = Application::get
            ($application);
            self::sendResponse
            (data: $applications);
        } else {
            self::sendResponse
            (code: 403, error: 'Missing, invalid or expired token.');
        }
    }

    public static function apply(array $params, array $data): void
    {
        if (
            self::checkToken
            ($data)
        ) {
            $userId = $data
            ['api_user'];
            $listingId = $data
            ['listingId'];
            $user = new User(id: $userId);
            $listing = new Listing(id: $listingId);
            $application = new Application(listingId: $listing->getId(), userId: $user->getId());
            $application = Application::save
            ($application);
            self::sendResponse
            (data: $application, code: 201);
        } else {
            self::sendResponse
            (code: 403, error: 'Missing, invalid or expired token.');
        }
    }
}