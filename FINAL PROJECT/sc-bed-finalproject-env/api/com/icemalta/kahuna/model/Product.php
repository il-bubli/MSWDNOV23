<?php
namespace com\icemalta\kahuna\model;

require_once 'com/icemalta/kahuna/model/DBConnect.php';

use DateInterval;
use DateTime;
use \PDO;
use \JsonSerializable;
use com\icemalta\kahuna\model\DBConnect;

class Product implements JsonSerializable
{
    protected static $db;
    protected ?int $id = 0;
    protected ?string $serial;
    protected ?string $name;
    protected ?int $warrantyLength = 0;

    public function __construct(?string $serial = null, ?string $name = null, ?int $warrantyLength = 0, ?int $id = 0)
    {
        $this->serial = $serial;
        $this->name = $name;
        $this->warrantyLength = $warrantyLength;
        $this->id = $id;
        self::$db = DBConnect::getInstance()->getConnection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getSerial(): string
    {
        return $this->serial;
    }

    public function setSerial(string $serial): self
    {
        $this->serial = $serial;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getWarrantyLength(): int
    {
        return $this->warrantyLength;
    }

    public function setWarrantyLength(int $warrantyLength): self
    {
        $this->warrantyLength = $warrantyLength;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public static function save(Product $product): Product
    {
        if ($product->getId() === 0) {
            //New product (insert)
            $sql = 'INSERT INTO Product(serial, name, warrantyLength) VALUES (:serial, :name, :warrantyLength)';
            $sth = self::$db->prepare($sql);
        } else {
            // Update product (update)
            $sql = 'UPDATE Product SET serial = :serial, name = :name, warrantyLength = :warrantyLength WHERE id = :id';
            $sth = self::$db->prepare($sql);
            $sth->bindValue('id', $product->getId());
        }
        $sth->bindValue('name', $product->getName());
        $sth->bindValue('serial', $product->getSerial());
        $sth->bindValue('warrantyLength', $product->getWarrantyLength());
        $sth->execute();

        if ($sth->rowCount() > 0 && $product->getId() === 0) {
            $product->setId(self::$db->lastInsertId());
        }

        return $product;
    }

    public static function loadAll(): array
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT serial, name, warrantyLength, id FROM Product';
        $sth = self::$db->prepare($sql);
        $sth->execute();
        $products = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Product(...$fields));
        return $products;
    }

    public static function productSerialExists(array $products, $serial): bool {
        $serialNumbers = array_column($products, 'serial');
        return in_array($serial, $serialNumbers);
    }

    public static function load(string $productSerial): Product|array
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT serial, name, warrantyLength, id FROM Product WHERE serial = :serial';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('serial', $productSerial);
        $sth->execute();
        $product = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Product(...$fields));
        return $product[0];
    }

    public static function delete(Product $product): bool
    {
        $sql = 'DELETE FROM Product WHERE id = :id';
        $sth = self::$db->prep($sql);
        $sth->bindvalue('id', $product->getId());
        $sth->execute();
        return $sth->rowCount() > 0;
    }
      public function isProductValid(): bool
    {
        $sql = 'SELECT id FROM Product WHERE serial = :serial';
        $sth = $this->db->getConnection->prepare($sql);
        $sth->bindValue('serial', $this->serial);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_OBJ);
        return $result;
    }
}

class RegisteredProduct extends Product implements JsonSerializable
{

    protected int $registeredId = 0;
    protected int $userId = 0;
    protected DateTime|string|null $purchaseDate = null;
    protected DateTime|string|null $warrantyEndDate;

    public function __construct(?string $serial = null, ?string $name = null, ?int $warrantyLength = 0, int $userId = 0, string $purchaseDate = null, string $warrantyEndDate = null, ?int $id = 0, ?int $registeredId = 0) {
        parent::__construct($serial, $name, $warrantyLength, $id);
        $dateToday = new DateTime('now');
        $this->registeredId = $registeredId;
        $this->id = $id;
        $this->userId = $userId;
        $this->purchaseDate = $dateToday->format('y-m-d');
        $warrantyEndDate = $dateToday->add(new DateInterval("P{$warrantyLength}Y"));
        $this->warrantyEndDate = $warrantyEndDate->format('y-m-d');
        $this->id = $id;
        self::$db = DBConnect::getInstance()->getConnection();
        }


    public function getRegisteredId(): int
    {
        return $this->registeredId;
    }

    public function setRegisteredId(int $registeredId): self
    {
        $this->registeredId = $registeredId;
        return $this;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }
    public function getPurchaseDate(): ?string
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(string $purchaseDate): self
    {
        $this->purchaseDate = $purchaseDate;
        return $this;
    }

    public function getWarrantyEndDate(): ?string
    {
        return $this->warrantyEndDate;
    }

    public function setWarrantyEndDate(string $warrantyEndDate): self
    {
        $this->warrantyEndDate = $warrantyEndDate;
        return $this;
    }

  


    public static function loadallRegisteredProducts(): array
    {
        $sql = 'SELECT Product.serial FROM ProductOwner JOIN Product ON ProductOwner.productId = Product.id';
        $sth = self::$db->prepare($sql);
        $sth->execute();
        $products = $sth->fetchAll(PDO::FETCH_COLUMN, 0);
        return $products;
    }

    public static function loadRegisteredProducts(RegisteredProduct $registeredProduct): array
    {
        $sql = 'SELECT Product.serial, Product.name, Product.warrantyLength, ProductOwner.userId, ProductOwner.purchaseDate, ProductOwner.warrantyEndDate, ProductOwner.productId, ProductOwner.id FROM ProductOwner JOIN Product ON ProductOwner.productId = Product.id WHERE ProductOwner.userId = :userId';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('userId', $registeredProduct->getUserId());
        $sth->execute();
        $products = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new RegisteredProduct(...$fields));
        return $products;
    }

    public static function saveRegisteredProduct(RegisteredProduct $registeredProduct): Product
    {
        if ($registeredProduct->getRegisteredId() === 0) {
            //New product owner (insert)
            $sql = 'INSERT INTO ProductOwner(productId, userId, purchaseDate, warrantyEndDate) VALUES (:productId, :userId, :purchaseDate, :warrantyEndDate)';
            $sth = self::$db->prepare($sql);
        } else {
            // Update product owner (update)
            $sql = 'UPDATE ProductOwner SET productId = :productId, userId = :userId, purchaseDate = :purchaseDate, warrantyEndDate = :warrantyEndDate WHERE id = :id';
            $sth = self::$db->prepare($sql);
            $sth->bindValue('id', $registeredProduct->getregisteredId());
        }
        $sth->bindValue('productId', $registeredProduct->getId());
        $sth->bindValue('userId', $registeredProduct->getUserId());
        $sth->bindValue('purchaseDate', $registeredProduct->getPurchaseDate());
        $sth->bindValue('warrantyEndDate', $registeredProduct->getWarrantyEndDate());
        $sth->execute();

        if ($sth->rowCount() > 0 && $registeredProduct->getRegisteredId() === 0) {
            $registeredProduct->setRegisteredId(self::$db->lastInsertId());
        }

        return $registeredProduct;
        
    }

    public function isProductRegistered(): bool
    {
        $sql = 'SELECT ProductOwner.userId FROM ProductOwner JOIN Product ON ProductOwner.productId = Product.id WHERE Product.serial = :serial';;
        $sth = $this->db->getConnection->prepare($sql);
        $sth->bindValue('serial', $this->serial);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_OBJ);
        return $result;
    }


}

