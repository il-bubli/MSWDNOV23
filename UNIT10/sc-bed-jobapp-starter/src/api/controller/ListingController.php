<?php
namespace com\icemalta\jobapp\api\controller;

use com\icemalta\jobapp\api\model\Listing;

class ListingController extends Controller
{

    public static function getAll(array $params, array $data): void
    {
        if (self::checkToken($data)) {
            $listings = Listing::getAll();
            self::sendResponse(data: $listings);
            } else {
            self::sendResponse(code: 403, error: 'Missing, invalid or expired token.');
            }
    }

    public static function get(array $params, array $data): void
    {
        if (self::checkToken($data)) {
            $listing = new Listing(id: $params['id']);
            $listings = Listing::get($listing);
            self::sendResponse(data: $listings);
            } else {
            self::sendResponse(code: 403, error: 'Missing, invalid or expired token.');
            }
    }
}