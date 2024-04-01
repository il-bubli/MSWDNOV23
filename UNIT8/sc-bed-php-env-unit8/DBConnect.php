<?php

class DBConnect
{
    private static $singleton = null;
    private $dbh;

    private function __construct()
    {
        $this->dbh = new PDO("mysql:host=mariadb;dbname=ShoppingCart","root","root",array(PDO::ATTR_PERSISTENT => true));

    }

    public static function getInstance()
    {
        self::$singleton = self::$singleton ?? new DBConnect();
        return self::$singleton;
    }

    public function getConnection()
    {
        return $this->dbh;
    }
}