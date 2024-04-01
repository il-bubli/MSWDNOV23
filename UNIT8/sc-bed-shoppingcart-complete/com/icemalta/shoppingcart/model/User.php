<?php
namespace com\icemalta\shoppingcart\model;

use \PDO;
use com\icemalta\shoppingcart\model\DBConnect;

class User
{
    private $db;
    private ?int $id;
    private ?string $firstName;
    private ?string $lastName;
    private string $email;
    private string $password;
    private string $accessLevel = 'standard';

    public function __construct(string $email, string $password, ?string $firstName = null, ?string $lastName = null, ?int $id = null, ?string $accessLevel = 'standard')
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->id = $id;
        $this->accessLevel = $accessLevel;
        $this->db = DBConnect::getInstance();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getAccessLevel(): string
    {
        return $this->accessLevel;
    }

    public function setAccessLevel(string $accessLevel): self
    {
        $this->accessLevel = $accessLevel;
        return $this;
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'accessLevel' => $this->accessLevel
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->firstName = $data['firstName'];
        $this->lastName = $data['lastName'];
        $this->email = $data['email'];
        $this->accessLevel = $data['accessLevel'];
        $this->db = DBConnect::getInstance();
    }

    public function save(): int
    {
        $hashed = password_hash($this->password, PASSWORD_DEFAULT);

        if ($this->id === null) {
            //Add a new user
            $sql = 'INSERT INTO User(firstName, lastName, email, password, accesslevel) VALUES (:firstName, :lastName, :email, :password, :accessLevel)';
            $sth = $this->db->getConnection()->prepare($sql);
        } else {
            //Updating an existing user
            $sql = 'UPDATE User SET firstName = :firstName, lastName = :lastName, email = :email, password = :password, accessLevel = :accessLevel WHERE id=:id';
            $sth = $this->db->getConnection()->prepare($sql);
            $sth->bindValue('id', $this -> id);
        }

        $sth->bindValue('firstName', $this->firstName);
        $sth->bindValue('lastName', $this->lastName);
        $sth->bindValue('email', $this->email);
        $sth->bindValue('password', $hashed);
        $sth->bindValue('accessLevel', $this->accessLevel);
        $sth->execute();

        if ($this->id === null) {
            $this->id = $this->db->getConnection()->lastInsertId();
        }
        return $sth->rowCount();
    }

    public function login(): bool
    {
        $sql = 'SELECT * FROM User WHERE email = :email';
        $sth = $this->db->getConnection()->prepare($sql);
        $sth->bindValue('email', $this->email);
        $sth->execute();

        $result = $sth->fetch(PDO::FETCH_OBJ);
        if ($result && password_verify($this->password, $result->password)) {
            $this->id = $result->id;
            $this->firstName = $result->firstName;
            $this->lastName = $result->lastName;
            $this->email = $result->email;
            $this->accessLevel = $result->accessLevel;
            return true;
        }
        return false;


    }

    public function isEmailAvailable(): bool
    {
        $sql = 'SELECT id FROM User WHERE email = :email';
        $sth = $this->db->getConnection()->prepare($sql);
        $sth->bindValue('email', $this->email);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_OBJ);

        return !$result;
    }
}