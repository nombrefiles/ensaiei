<?php

namespace Source\Models;

use PDO;
use Source\Core\Connect;
use Source\Enums\Role;

class User
{
    private $id;
    private $role;
    private $name;
    private $email;
    private $password;
    private $photo;
    private $username;
    private $bio;
    private $deleted;
    private $emailVerified;
    private $verificationCode;
    private $verificationCodeExpires;
    private $errorMessage;

    public function __construct(
        int $id = null,
        Role $role = null,
        string $name = null,
        string $email = null,
        string $password = null,
        string $photo = null,
        string $username = null,
        string $bio = null,
        bool $deleted = false,
        bool $emailVerified = false,
        string $verificationCode = null,
        string $verificationCodeExpires = null
    ) {
        $this->id = $id;
        $this->role = $role;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->photo = $photo;
        $this->username = $username;
        $this->bio = $bio;
        $this->deleted = $deleted;
        $this->emailVerified = $emailVerified;
        $this->verificationCode = $verificationCode;
        $this->verificationCodeExpires = $verificationCodeExpires;
    }

    public function findAll()
    {
        $stmt = Connect::getInstance()->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUsername(string $username): bool
    {
        try {
            $stmt = Connect::getInstance()
                ->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
            $stmt->bindValue(":username", $username);
            $stmt->execute();

            if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->fill($user);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            $this->errorMessage = "Erro ao buscar usuário: " . $e->getMessage();
            return false;
        }
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
                    (role, name, email, password, photo, username, bio, deleted, email_verified, verification_code, verification_code_expires)
                VALUES 
                    (:role, :name, :email, :password, :photo, :username, :bio, :deleted, :email_verified, :verification_code, :verification_code_expires)
            ");

            $stmt->bindValue(":role", $this->role->value);
            $stmt->bindValue(":name", $this->name);
            $stmt->bindValue(":email", $this->email);
            $stmt->bindValue(":password", $this->password);
            $stmt->bindValue(":photo", $this->photo);
            $stmt->bindValue(":username", $this->username);
            $stmt->bindValue(":bio", $this->bio);
            $stmt->bindValue(":deleted", $this->deleted ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(":email_verified", $this->emailVerified ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(":verification_code", $this->verificationCode);
            $stmt->bindValue(":verification_code_expires", $this->verificationCodeExpires);

            if ($stmt->execute()) {
                $this->id = Connect::getInstance()->lastInsertId();
                return true;
            }
            return false;
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
                    role = :role,
                    name = :name,
                    email = :email,
                    password = :password,
                    photo = :photo,
                    bio = :bio,
                    username = :username,
                    deleted = :deleted,
                    email_verified = :email_verified,
                    verification_code = :verification_code,
                    verification_code_expires = :verification_code_expires
                WHERE id = :id
            ");

            $stmt->bindValue(":role", $this->role->value);
            $stmt->bindValue(":name", $this->name);
            $stmt->bindValue(":email", $this->email);
            $stmt->bindValue(":password", $this->password);
            $stmt->bindValue(":photo", $this->photo);
            $stmt->bindValue(":bio", $this->bio);
            $stmt->bindValue(":username", $this->username);
            $stmt->bindValue(":deleted", $this->deleted ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(":email_verified", $this->emailVerified ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(":verification_code", $this->verificationCode);
            $stmt->bindValue(":verification_code_expires", $this->verificationCodeExpires);
            $stmt->bindValue(":id", $this->id);

            return $stmt->execute();
        } catch (\PDOException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    public function delete()
    {
        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindValue(":id", $this->id);
            return $stmt->execute();
        } catch (\PDOException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    public function fill(array $data)
    {
        $this->id       = $data["id"] ?? null;
        $this->role     = isset($data["role"]) ? Role::from($data["role"]) : null;
        $this->name     = $data["name"] ?? null;
        $this->email    = $data["email"] ?? null;
        $this->password = $data["password"] ?? null;
        $this->photo    = $data["photo"] ?? null;
        $this->username = $data["username"] ?? null;
        $this->bio      = $data["bio"] ?? null;
        $this->deleted  = isset($data["deleted"]) ? (bool)$data["deleted"] : false;
        $this->emailVerified = isset($data["email_verified"]) ? (bool)$data["email_verified"] : false;
        $this->verificationCode = $data["verification_code"] ?? null;
        $this->verificationCodeExpires = $data["verification_code_expires"] ?? null;
    }

    public function getId() { return $this->id; }
    public function getRole() { return $this->role; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getPhoto() { return $this->photo; }
    public function getUsername() { return $this->username; }
    public function getBio() { return $this->bio; }
    public function getDeleted(): bool { return (bool)$this->deleted; }
    public function getEmailVerified(): bool { return (bool)$this->emailVerified; }
    public function getVerificationCode() { return $this->verificationCode; }
    public function getVerificationCodeExpires() { return $this->verificationCodeExpires; }
    public function getErrorMessage() { return $this->errorMessage; }

    public function setRole($role) { $this->role = $role; }
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = $password; }
    public function setPhoto($photo) { $this->photo = $photo; }
    public function setUsername($username) { $this->username = $username; }
    public function setBio($bio) { $this->bio = $bio; }
    public function setDeleted($deleted) { $this->deleted = (bool)$deleted; }
    public function setEmailVerified($emailVerified) { $this->emailVerified = (bool)$emailVerified; }
    public function setVerificationCode($code) { $this->verificationCode = $code; }
    public function setVerificationCodeExpires($expires) { $this->verificationCodeExpires = $expires; }
}