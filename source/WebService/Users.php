<?php

namespace Source\WebService;

use Exception;
use PDO;
use Source\Core\Connect;
use Source\Models\User;
use Source\Core\JWTToken;

class Users extends Api
{
    public function listUsers (): void
    {
        $users = new User();
        $this->call(200, "success", "Lista de usuários", "success")
            ->back($users->findAll());
    }

    public function createUser(array $data)
    {

        // verifica se os dados estão preenchidos
        if(in_array("", $data)){
            $this->call(400, "bad_request", "Dados inválidos", "error")->back();
            return;
        }

        $user = new User(
            null,
            $data["idType"] ?? null,
            $data["name"] ?? null,
            $data["email"] ?? null,
            $data["password"] ?? null,
            $data["photo"] ?? "https://upload.wikimedia.org/wikipedia/commons/0/03/Twitter_default_profile_400x400.png",
            $data["username"] ?? null,
            $data["bio"] ?? "Eu amo teatro!",
            false
        );

        if(!$user->insert()){
            $this->call(500, "internal_server_error", $user->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "username" => $user->getUsername(),
        ];

        $this->call(201, "created", "Usuário criado com sucesso", "success")
            ->back($response);
    }

    public function listUserByUsername(array $data): void
    {
        if (!isset($data["username"])) {
            $this->call(400, "bad_request", "Username inválido", "error")->back();
            return;
        }

        $user = new User();

        if (!$user->findByUsername($data["username"])) {
            $this->call(404, "not_found", "Usuário não encontrado", "error")->back();
            return;
        }

        ob_clean();

        error_log("Status deleted: " . var_export($user->getDeleted(), true));

        if ($user->getDeleted()) {
            header('Content-Type: text/html; charset=utf-8');

            $deletedProfilePath = __DIR__ . "/../../design/html/deleted-profile.php";

            if (!file_exists($deletedProfilePath)) {
                error_log("Arquivo não encontrado: " . $deletedProfilePath);
                $this->call(500, "internal_server_error", "Template não encontrado", "error")->back();
                return;
            }

            $username = htmlspecialchars($data["username"]);
            include $deletedProfilePath;
            return;
        }

        $name = htmlspecialchars($user->getName());
        $username = htmlspecialchars($data["username"]);
        $email = htmlspecialchars($user->getEmail());
        $photo = htmlspecialchars($user->getPhoto() ?? "../assets/images/default-profile.png");
        $bio = htmlspecialchars($user->getBio() ?? "Eu amo teatro!");

        header('Content-Type: text/html; charset=utf-8');

        $profilePath = __DIR__ . "/../../design/html/profile.php";

        include $profilePath;
    }


    public function updateUser(array $data): void
    {
        $this->auth();

        if (!isset($this->userAuth->id)) {
            $this->call(400, "bad_request", "ID do usuário não encontrado", "error")->back();
            return;
        }

        $user = new User();
        if (!$user->findById($this->userAuth->id)) {
            $this->call(404, "not_found", "Usuário não encontrado", "error")->back();
            return;
        }

        if (isset($data["idType"])) {
            if (!filter_var($data["idType"], FILTER_VALIDATE_INT)) {
                $this->call(400, "bad_request", "Tipo de usuário inválido", "error")->back();
                return;
            }
            $user->setIdType($data["idType"]);
        }

        if (isset($data["name"])) {
            if (empty($data["name"])) {
                $this->call(400, "bad_request", "Nome não pode ser vazio", "error")->back();
                return;
            }
            $user->setName($data["name"]);
        }

        if (isset($data["email"])) {
            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                $this->call(400, "bad_request", "Email inválido", "error")->back();
                return;
            }
            $user->setEmail($data["email"]);
        }

        if (isset($data["password"])) {
            if (empty($data["password"])) {
                $this->call(400, "bad_request", "Senha não pode ser vazia", "error")->back();
                return;
            }
            $user->setPassword(password_hash($data["password"], PASSWORD_DEFAULT));
        }

        if (isset($data["photo"])) {
            $user->setPhoto($data["photo"]);
        }

        if (isset($data["bio"])) {
            if (empty($data["bio"])) {
                $this->call(400, "bad_request", "Biografia não pode estar em branco!", "error")->back();
                return;
            }
            $user->setBio($data["bio"]);
        }

        if (!$user->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao atualizar usuário: " . $user->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $user->getId(),
            "idType" => $user->getIdType(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "photo" => $user->getPhoto(),
            "bio" => $user->getBio(),
            "username" => $user->getUsername(),
            "deleted" => $user->getDeleted()
        ];

        $this->call(200, "success", "Usuário atualizado com sucesso", "success")->back($response);
    }

    public function login(array $data): void
    {
        if (( empty($data["username"]) && empty($data["email"]) )|| empty($data["password"])) {
            $this->call(400, "bad_request", "Credenciais inválidas", "error")->back();
            return;
        }

        $user = new User();

        if(isset($data['email']) && !$user->findByEmail($data["email"])){
            $this->call(401, "unauthorized", "Usuário não encontrado", "error")->back();
            return;
        }

        if(isset($data["username"]) && !$user->findByUsername($data["username"])){
            $this->call(401, "unauthorized", "Usuário não encontrado", "error")->back();
            return;
        }

        if(!password_verify($data["password"], $user->getPassword())){
            $this->call(401, "unauthorized", "Senha inválida", "error")->back();
            return;
        }

        // Gerar o token JWT
        $jwt = new JWTToken();
        $token = $jwt->create([
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "name" => $user->getName()
        ]);

        // Retornar o token JWT na resposta
        $this->call(200, "success", "Login realizado com sucesso", "success")
            ->back([
                "token" => $token,
                "user" => [
                    "id" => $user->getId(),
                    "name" => $user->getName(),
                    "email" => $user->getEmail(),
                    "photo" => $user->getPhoto()
                ]
            ]);

    }

    public function deleteUser()
    {
        $this->auth();

        $user = new User();
        if (!$user->findById($this->userAuth->id)) {
            $this->call(404, "not_found", "Usuário não encontrado", "error")->back();
            return;
        }

        $user->setDeleted(true);

        if (!$user->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao deletar usuário", "error")->back();
            return;
        }

        $this->call(200, "success", "Usuário deletado com sucesso", "success")->back();
    }

}