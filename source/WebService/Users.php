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
        //var_dump($users->findAll());
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
            username: $data["username"] ?? null,
        );

        if(!$user->insert()){
            $this->call(500, "internal_server_error", $user->getErrorMessage(), "error")->back();
            return;
        }
        // montar $response com as informações necessárias para mostrar no front
        $response = [
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "photo" => $user->getPhoto()
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

        // Dados do usuário
        $name = $user->getName();
        $username = $data["username"];
        $email = $user->getEmail();
        $photo = $user->getPhoto() ?? "../assets/images/default-profile.png";
        $followers = 0;
        $following = 0;
        $bio = "Sem biografia";

        header('Content-Type: text/html; charset=utf-8');

        // Renderiza o HTML com os dados
        include __DIR__ . "/../../design/html/profile.php";
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

        if (isset($data["deleted"])) {
            if (!is_bool($data["deleted"]) && !in_array($data["deleted"], [0, 1, '0', '1'])) {
                $this->call(400, "bad_request", "Valor inválido para deleted", "error")->back();
                return;
            }
            $user->setDeleted((bool)$data["deleted"]);
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
            "deleted" => $user->getDeleted()
        ];

        $this->call(200, "success", "Usuário atualizado com sucesso", "success")->back($response);
    }

    public function login(array $data): void
    {
        // Verificar se os dados de login foram fornecidos
        if (empty($data["email"]) || empty($data["password"])) {
            $this->call(400, "bad_request", "Credenciais inválidas", "error")->back();
            return;
        }

        $user = new User();

        if(!$user->findByEmail($data["email"])){
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

    public function deleteUser(array $data)
    {
        $data["deleted"] = true;
        $this->updateUser($data);

    }

}