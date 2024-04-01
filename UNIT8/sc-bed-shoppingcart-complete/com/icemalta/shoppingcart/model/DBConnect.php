<?php
namespace com\icemalta\shoppingcart\model;

use \PDO;

class DBConnect
{
    private static $singelton = null;
    private $dbh;

    private function __construct()
    {
        $env = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/.env');
        $this->dbh = new PDO("mysql:host=mariadb;dbname=ShoppingCart",
        $env["DB_USER"],
        $env["DB_PASS"],
        array(PDO::ATTR_PERSISTENT => true)
        );
    }

    public static function getInstance()
    {
        self::$singelton = self::$singelton ?? new DBConnect();
        return self::$singelton;
    }

    public function __serialize(): array
    {
        return [];
    }

    public function __unserialize(array $data): void
    {

    }

    public function getConnection()
    {
        return $this->dbh;
    }
}