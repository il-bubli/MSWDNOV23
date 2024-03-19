<?php
namespace com\icemalta\shoppingcart\model;

class Product
{
    private int $id;
    private string $name;
    private float $price;
    private string $description;
    private ?string $featuredImage = null;
    private bool $requiresDeposit = false;

    public function __construct(int $id, string $name, float $price, string $description, ?string $featuredImage = null, bool $requiresDeposit = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->featuredImage = $featuredImage;
        $this->requiresDeposit = $requiresDeposit;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getPrice(): string
    {
        return $this->price;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getFeaturedImage(): ?string
    {
        return $this->featuredImage;
    }
    public function requiresDeposit(): bool
    {
        return $this->requiresDeposit;
    }
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
    public function setFeaturedImage(?string $featuredImage): self
    {
        $this->featuredImage = $featuredImage;
        return $this;
    }
    public function setRequiresDeposit(bool $requiresDeposit): self
    {
        $this->requiresDeposit = $requiresDeposit;
        return $this;
    }
    
}