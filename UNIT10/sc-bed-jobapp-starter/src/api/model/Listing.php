<?php
namespace com\icemalta\jobapp\api\model;

use \JsonSerializable;
use \PDO;
use \DateTime;
use com\icemalta\jobapp\api\model\DBConnect;

class Listing implements JsonSerializable
{
    private static $db;
    private ?int $id;
    private ?string $jobTitle;
    private ?string $company;
    private ?string $location;
    private ?string $description;
    private ?string $requirements;
    private ?string $companyImage;
    private DateTime|string|null $postDate;
    private ?string $wageIndication;

    public function __construct(?string $jobTitle = null, ?string $company = null, ?string $location = null, ?string $description = null, ?string $requirements = null, ?string $companyImage = null, DateTime|string $postDate = null, ?string $wageIndication = null, ?int $id = 0)
    {
        $this->jobTitle = $jobTitle;
        $this->company = $company;
        $this->location = $location;
        $this->description = $description;
        $this->requirements = $requirements;
        $this->companyImage = $companyImage;
        $this->postDate = $postDate;
        $this->wageIndication = $wageIndication;
        $this->id = $id;
        self::$db = DBConnect::getInstance()->getConnection();
    }

    public static function get(Listing $listing): Listing|array
    {
        $sql = 'SELECT jobTitle, company, location, description, requirements, companyImage, postDate, wageIndication, id FROM Listing WHERE id = :id ORDER BY postDate DESC';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('id', $listing->getId());
        $sth->execute();
        $listing = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Listing(...$fields));
        return count($listing) > 0 ? $listing[0] : [];
    }

    public static function getAll(): array
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT jobTitle, company, location, description, requirements, companyImage, postDate, wageIndication, id FROM Listing ORDER BY postDate DESC';
        $sth = self::$db->prepare($sql);
        $sth->execute();
        $listings = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Listing(...$fields));
        return $listings;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
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

    public function getJobTitle(): string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;
        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
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

    public function getRequirements(): string
    {
        return $this->requirements;
    }

    public function setRequirements(string $requirements): self
    {
        $this->requirements = $requirements;
        return $this;
    }

    public function getCompanyImage(): string
    {
        return $this->companyImage;
    }

    public function setCompanyImage(string $companyImage): self
    {
        $this->companyImage = $companyImage;
        return $this;
    }

    public function getPostDate(): DateTime
    {
        return $this->postDate;
    }

    public function setPostDate(DateTime $postDate): self
    {
        $this->postDate = $postDate;
        return $this;
    }

    public function getWageIndication(): string
    {
        return $this->wageIndication;
    }

    public function setWageIndication(string $wageIndication): self
    {
        $this->wageIndication = $wageIndication;
        return $this;
    }
}