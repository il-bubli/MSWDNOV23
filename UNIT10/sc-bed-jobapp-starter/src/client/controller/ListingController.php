<?php
namespace com\icemalta\jobapp\client\controller;

use com\icemalta\jobapp\client\controller\Controller;

class ListingController extends Controller
{

    public static function getListings($params, $data): object
    {
        $listings = json_decode(self::req('GET', '/listing', $data));
        return $listings;
    }

    public static function getListing(array $params, array $data): object
    {
        $listing = json_decode(self::req('GET', "/listing/{$params['id']}", $data));
        return $listing;
    }
}