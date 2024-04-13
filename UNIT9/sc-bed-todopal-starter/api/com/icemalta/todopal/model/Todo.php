<?php
namespace com\icemalta\todopal\model;

use \PDO;
use \JsonSerializable;
use com\icemalta\todopal\model\DBConnect;

class Todo implements JsonSerializable
{

    private static $db;
    private int|string $id = 0;
    private int $userId;
    private ?string $title;
    private int|string $birth = 0;
    private bool $complete = false;
    private ?string $notes;

    public function __construct(public int $todoUserId, ?string $title = null, ?string $notes = null, int|string $birth = 0, ?bool $complete = false, public int|string $todoId = 0)
    {
        $this->userId = $todoUserId;
        $this->title = $title;
        $this->birth = $birth;
        $this->complete = $complete;
        $this->notes = $notes;
        $this->id = $todoId;
        self::$db = DBConnect::getInstance()->getConnection();
    }

    public static function save(Todo $todo): Todo
    {
        if ($todo->getId() === 0) {
            // Insert
            $sql = 'INSERT INTO Todo(userId, title, notes) VALUES (:userId, :title, :notes)';
            $sth = self::$db->prepare($sql);
            $sth->bindValue('userId', $todo->getUserId());
        } else {
            // Update
            $sql = 'UPDATE Todo SET title = :title, complete = :complete, notes = :notes WHERE id = :id';
            $sth = self::$db->prepare($sql);
            $sth->bindValue('id', $todo->getId());
            $sth->bindValue('complete', $todo->isComplete(), PDO::PARAM_INT);
        }
        $sth->bindValue('title', $todo->getTitle());
        $sth->bindValue('notes', $todo->getNotes());
        $sth->execute();
        if ($sth->rowCount() > 0 && $todo->getId() === 0) {
            $todo->setId(self::$db->lastInsertId());
        }
        return $todo;
    }

    public static function load(Todo $todo): array
    {
        $sql = 'SELECT userId, title, notes, birth, complete, id FROM Todo WHERE userId = :userId ORDER BY birth DESC';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('userId', $todo->getUserId());
        $sth->execute();
        $todos = $sth->fetchAll(PDO::FETCH_FUNC, fn(...$fields) => new Todo(...$fields));
        return $todos;
    }

    public static function delete(Todo $todo): bool
    {
        $sql = 'DELETE FROM Todo WHERE id = :id';
        $sth = self::$db->prepare($sql);
        $sth->bindValue('id', $todo->getId());
        $sth->execute();
        return $sth->rowCount() > 0;
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

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getBirth(): ?int
    {
        return $this->birth;
    }

    public function setBirth(int $birth): self
    {
        $this->birth = $birth;
        return $this;
    }

    public function isComplete(): bool
    {
        return $this->complete;
    }

    public function setComplete(bool $complete): self
    {
        $this->complete = $complete;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }
}