<?php
namespace com\icemalta\kahuna\model;

require_once 'com/icemalta/kahuna/model/DBConnect.php';

use DateTime;
use DateTimeZone;
use \PDO;
use \JsonSerializable;
use com\icemalta\kahuna\model\DBConnect;

//Modified from todopal in UNIT 9 with the help of chatGPT.
class Ticket implements JsonSerializable
{
    protected static $db;
    protected ?int $id = 0;
    protected ?int $userId = 0;
    protected ?int $productId = 0;
    protected ?string $issueDescription;
    protected ?string $status = "open";
    protected DateTime|string $submissionDate;

    public function __construct(?int $userId = 0, ?int $productId = 0, ?string $issueDescription = null, ?string $status = "open", DateTime|string $submissionDate = null, ?int $id = 0)
    {
        $dateToday = new DateTime('now');
        $dateToday->setTimezone(new DateTimeZone('Europe/Malta'));
        $this->id = $id;
        $this->userId = $userId;
        $this->productId = $productId;
        $this->issueDescription = $issueDescription;
        $this->status = $status;
        $this->submissionDate = $dateToday->format('Y-m-d H:i:s');
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

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function getIssueDescription(): string
    {
        return $this->issueDescription;
    }

    public function setIssueDescription(string $issueDescription): self
    {
        $this->issueDescription = $issueDescription;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }
    public function getSubmissionDate(): string
    {
        return $this->submissionDate;
    }

    public function setSubmissionDate(int $submissionDate): self
    {
        $this->submissionDate = $submissionDate;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
    public static function save(Ticket $ticket): Ticket
    {
        if ($ticket->getId() === 0) {
            //New ticket (insert)
            $sql = 'INSERT INTO Ticket(userId, productId, issueDescription, status, submissionDate) VALUES (:userId, :productId, :issueDescription, :status, :submissionDate)';
            $sth = self::$db->prepare($sql);
        } else {
            // Update ticket (update)
            $sql = 'UPDATE Ticket SET userId = :userId, productId = :productId, issueDescription = :issueDescription, status = :status, submissionDate = submissionDate WHERE id = :id';
            $sth = self::$db->prepare($sql);
            $sth->bindValue('id', $ticket->getId());
        }
        $sth->bindValue('userId', $ticket->getUserId());
        $sth->bindValue('productId', $ticket->getProductId());
        $sth->bindValue('issueDescription', $ticket->getIssueDescription());
        $sth->bindValue('status', $ticket->getStatus());
        $sth->bindValue('submissionDate', $ticket->getSubmissionDate());
        $sth->execute();

        if ($sth->rowCount() > 0 && $ticket->getId() === 0) {
            $ticket->setId(self::$db->lastInsertId());
        }

        return $ticket;
    }

    public static function loadAll(): array
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT userId, productId, issueDescription, status, submissionDate, id FROM Ticket';
        $sth = self::$db->prepare($sql);
        $sth->execute();
        $tickets = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Ticket(...$fields));
        return $tickets;
    }

    public static function loadUserTicket($userId, $productId): Ticket|null
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT userId, productId, issueDescription, status, submissionDate, id FROM Ticket WHERE userId = :userId AND productId = :productId';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('userId', $userId);
        $sth->bindValue('productId', $productId);
        $sth->execute();
        $ticket = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Ticket(...$fields));
        if (empty($ticket)) {
            return null;
        }
        return $ticket[0];
    }

    public static function load(Ticket $ticket): Ticket
{
    self::$db = DBConnect::getInstance()->getConnection();
    $sql = 'SELECT userId, productId, issueDescription, status, submissionDate, id FROM Ticket WHERE id = :id';
    $sth = self::$db->prepare($sql);
    $sth->bindValue('id', $ticket->getId());
    $sth->execute();
    $ticket = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Ticket(...$fields));
    return $ticket[0];
}

    public static function delete(Product $product): bool
    {
        $sql = 'DELETE FROM Product WHERE id = :id';
        $sth = self::$db->prep($sql);
        $sth->bindvalue('id', $product->getId());
        $sth->execute();
        return $sth->rowCount() > 0;
    }

}
