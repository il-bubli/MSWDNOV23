<?php
namespace com\icemalta\shoppingcart\model;

use \PDO;
use com\icemalta\shoppingcart\model\DBConnect;

class Product
{
    private DBConnect $db;
    private int $id;
    private string $name;
    private float $price;
    private string $description;
    private ?string $featuredImage = null;
    private bool $requiresDeposit = false;

    public function __construct(int $id, string $name, float $price, string $description, string $featuredImage = null, bool $requiresDeposit = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->featuredImage = $featuredImage;
        $this->requiresDeposit = $requiresDeposit;
        $this->db = DBConnect::getInstance();
    }

    public static function getAll(): array
    {
        $sql = 'SELECT * FROM Product';
        $sth = DBConnect::getInstance()->prepare($sql);
        $sth->execute();
        return $sth->fetchAll(
            PDO::FETCH_FUNC,
            fn(...$fields) => new Product(...$fields)
        );
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getFeaturedImage(): ?string
    {
        return $this->featuredImage;
    }

    public function setFeaturedImage(?string $featuredImage): self
    {
        $this->featuredImage = $featuredImage;
        return $this;
    }

    public function requiresDeposit(): bool
    {
        return $this->requiresDeposit;
    }

    public function setRequiresDeposit(bool $requiresDeposit): self
    {
        $this->requiresDeposit = $requiresDeposit;
        return $this;
    }

    public function save(): int
    {
        if ($this->id === null) {
            // Insert
            $sql = 'INSERT INTO Product(name, price, description, featuredImage, requiresDeposit)
 VALUES (:name, :price, :description, :featuredImage, :requiresDeposit)';
            $sth = $this->db->getConnection()->prepare($sql);
        } else {
            // Update
            $sql = 'UPDATE Product SET name = :name, price = :price, description = :description,
 featuredImage = :featuredImage, requiresDeposit = :requiresDeposit WHERE id = :id';
            $sth = $this->db->getConnection()->prepare($sql);
            $sth->bindValue('id', $this->id);
        }
        $sth->bindValue('name', $this->name);
        $sth->bindValue('price', $this->price);
        $sth->bindValue('description', $this->description);
        $sth->bindValue('featuredImage', $this->featuredImage);
        $sth->bindValue('requiresDeposit', $this->requiresDeposit, PDO::PARAM_BOOL);
        $sth->execute();
        return $sth->rowCount();
    }

}