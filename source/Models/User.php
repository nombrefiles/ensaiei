<?php

namespace Source\Models;

use PDO;
use Source\Core\Connect;

class User
{
    private $id;
    private $idType;
    private $name;
    private $email;
    private $password;
    private $photo;
    private $username;
    private $bio;
    private $deleted;
    private $errorMessage;

    public function __construct(
        $id = null,
        $idType = null,
        $name = null,
        $email = null,
        $password = null,
        $photo = null,
        $username = null,
        $bio = null,
        $deleted = false
    ) {
        $this->id = $id;
        $this->idType = $idType;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->photo = $photo;
        $this->username = $username;
        $this->bio = $bio;
        $this->deleted = $deleted;
    }

    // ===== CRUD METHODS =====

    public function findAll()
    {
        $stmt = Connect::getInstance()->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUsername($username)
    {
        $stmt = Connect::getInstance()->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->fill($user);
            return true;
        }

        return false;
    }

    public function findById($id)
    {
        $stmt = Connect::getInstance()->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->fill($user);
            return true;
        }

        return false;
    }

    public function findByEmail($email)
    {
        $stmt = Connect::getInstance()->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->fill($user);
            return true;
        }

        return false;
    }

    public function insert()
    {
        try {
            $stmt = Connect::getInstance()->prepare("
                INSERT INTO users 
                    (idType, name, email, password, photo, username, bio, deleted)
                VALUES 
                    (:idType, :name, :email, :password, :photo, :username, :bio, :deleted)
            ");

            $stmt->bindValue(":idType", $this->idType);
            $stmt->bindValue(":name", $this->name);
            $stmt->bindValue(":email", $this->email);
            $stmt->bindValue(":password", $this->password);
            $stmt->bindValue(":photo", $this->photo);
            $stmt->bindValue(":username", $this->username);
            $stmt->bindValue(":bio", $this->bio);
            $stmt->bindValue(":deleted", $this->deleted ? 1 : 0, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    public function updateById()
    {
        try {
            $stmt = Connect::getInstance()->prepare("
                UPDATE users SET
                    idType = :idType,
                    name = :name,
                    email = :email,
                    password = :password,
                    photo = :photo,
                    bio = :bio,
                    username = :username,
                    deleted = :deleted
                WHERE id = :id
            ");

            $stmt->bindValue(":idType", $this->idType);
            $stmt->bindValue(":name", $this->name);
            $stmt->bindValue(":email", $this->email);
            $stmt->bindValue(":password", $this->password);
            $stmt->bindValue(":photo", $this->photo);
            $stmt->bindValue(":bio", $this->bio);
            $stmt->bindValue(":username", $this->username);
            $stmt->bindValue(":deleted", $this->deleted ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(":id", $this->id);

            return $stmt->execute();
        } catch (\PDOException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    // ===== FILL OBJECT FROM ARRAY =====

    public function fill(array $data)
    {
        $this->id       = $data["id"] ?? null;
        $this->idType   = $data["idType"] ?? null;
        $this->name     = $data["name"] ?? null;
        $this->email    = $data["email"] ?? null;
        $this->password = $data["password"] ?? null;
        $this->photo    = $data["photo"] ?? null;
        $this->username = $data["username"] ?? null;
        $this->bio      = $data["bio"] ?? null;
        $this->deleted  = isset($data["deleted"]) ? (bool)$data["deleted"] : false;
    }

// ===== GETTERS =====

    public function getId()
    {
        return $this->id;
    }

    public function getIdType()
    {
        return $this->idType;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function getDeleted(): bool
    {
        return (bool)$this->deleted;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

// ===== SETTERS =====

    public function setIdType($idType)
    {
        $this->idType = $idType;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = (bool)$deleted;
    }

}
