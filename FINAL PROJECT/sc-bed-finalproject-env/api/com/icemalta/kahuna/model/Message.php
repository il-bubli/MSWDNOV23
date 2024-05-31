<?php
namespace com\icemalta\kahuna\model;

require_once 'com/icemalta/kahuna/model/DBConnect.php';

use DateTime;
use \PDO;
use \JsonSerializable;
use com\icemalta\kahuna\model\DBConnect;

//Modified from todopal in UNIT 9 with the help of chatGPT.

class Message implements JsonSerializable
{

    protected static $db;
    private ?int $id = 0;
    private ?int $ticketId = 0;
    private ?int $userId = 0;
    private ?string $message = null;
    private DateTime|string $timestamp;

    // Constructor
    public function __construct(?int $ticketId = null, ?int $userId = null, ?string $message = null, DateTime|string $timestamp = null, ?int $id = 0)
    {
        $this->id = $id;
        $this->ticketId = $ticketId;
        $this->userId = $userId;
        $this->message = $message;
        $this->timestamp = $timestamp;
        self::$db = DBConnect::getInstance()->getConnection();
    }

    // Getters and setters
    public function getid(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTicketId(): int
    {
        return $this->ticketId;
    }

    public function setTicketId(int $ticketId): self
    {
        $this->ticketId = $ticketId;
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

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(string $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public static function saveMessage(Message $message): Message
    {
        $sql = 'INSERT INTO Message(ticketId, userId, message, timestamp) VALUES (:ticketId, :userId, :message, :timestamp)';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('ticketId', $message->getTicketId());
        $sth->bindValue('userId', $message->getUserId());
        $sth->bindValue('message', $message->getMessage());
        $sth->bindValue('timestamp', $message->getTimestamp()->format('Y-m-d H:i:s')); // Assuming timestamp is a DateTime object
        $sth->execute();

        if ($sth->rowCount() > 0 && $message->getid() === null) {
            $message->setId(self::$db->lastInsertId());
        }

        return $message;
    }

    public static function loadTicketMessages(int $ticketId): array
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'SELECT ticketId,  userId, message, timestamp, id FROM Message WHERE ticketId = :ticketId';
        $sth = self::$db->prepare($sql);
        $sth->bindValue(':ticketId', $ticketId, PDO::PARAM_INT);
        $sth->execute();
        $messages = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Message(...$fields));
        return $messages;
    }

    public static function delete(int $messageId): bool
    {
        self::$db = DBConnect::getInstance()->getConnection();
        $sql = 'DELETE FROM Message WHERE id = :id';
        $sth = self::$db->prepare($sql);
        $sth->bindValue(':id', $messageId, PDO::PARAM_INT);
        $sth->execute();
        return $sth->rowCount() > 0;
    }

}