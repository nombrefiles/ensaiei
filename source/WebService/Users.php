<?php

namespace Source\WebService;

use Exception;
use PDO;
use SorFabioSantos\Uploader\Uploader;
use Source\Core\Connect;
use Source\Models\User;
use Source\Core\JWTToken;
use Source\Enums\Role;

class Users extends Api
{
    public function listUsers(): void
    {
        $users = new User();
        $this->call(200, "success", "Lista de usuários", "success")
            ->back($users->findAll());
    }

    public function createUser(array $data)
    {
        if (in_array("", $data)) {
            $this->call(400, "bad_request", "Dados inválidos", "error")->back();
            return;
        }

        try {
            $role = isset($data["role"]) ? Role::from($data["role"]) : Role::STANDARD;
        } catch (\ValueError $e) {
            $this->call(400, "bad_request", "Tipo de usuário inválido", "error")->back();
            return;
        }

        $user = new User(
            null,
            $role,
            $data["name"] ?? null,
            $data["email"] ?? null,
            isset($data["password"]) ? password_hash($data["password"], PASSWORD_DEFAULT) : null,
            $data["photo"] ?? "https://upload.wikimedia.org/wikipedia/commons/0/03/Twitter_default_profile_400x400.png",
            $data["username"] ?? null,
            $data["bio"] ?? "Eu amo teatro!",
            false
        );

        if ($user->findByEmail($data["email"])){
            $this->call(400, "bad_request", "E-mail já está sendo usado!", "error")->back();
            return;
        }

        if (!$user->insert()) {
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

        if ($user->getDeleted()) {
            header('Content-Type: text/html; charset=utf-8');

            $deletedProfilePath = __DIR__ . "/../../design/html/deleted-profile.php";

            if (!file_exists($deletedProfilePath)) {
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


    public function updatePhoto(): void
    {
        $this->auth();

        if (empty($_FILES["photo"]["name"])) {
            $this->call(400, "bad_request", "Nenhuma foto enviada", "error")->back();
            return;
        }

        $photo = $_FILES["photo"];

        $upload = new Uploader();
        $path = $upload->Image($photo);

        if (!$path) {
            $this->call(400, "bad_request", $upload->getMessage(), "error")->back();
            return;
        }

        $user = new User();
        if (!$user->findByEmail($this->userAuth->email)) {
            $this->call(404, "not_found", "Usuário não encontrado", "error")->back();
            return;
        }

        $user->setPhoto($path);
        if (!$user->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao atualizar foto: " . $user->getErrorMessage(), "error")->back();
            return;
        }

        $imageUrl = CONF_URL_BASE . ltrim(IMAGE_DIR, '/') . '/' . $user->getPhoto();

        $this->call(200, "success", "Foto atualizada com sucesso", "success")
            ->back([
                "photo" => $imageUrl
            ]);

    }


    public function updateUser(array $data = []): void
    {
        $this->auth();

        if (empty($data)) {
            $data = $this->getRequestData();
        }

        if (!isset($this->userAuth->id)) {
            $this->call(400, "bad_request", "ID do usuário não encontrado", "error")->back();
            return;
        }

        $user = new User();
        if (!$user->findById($this->userAuth->id)) {
            $this->call(404, "not_found", "Usuário não encontrado", "error")->back();
            return;
        }

        $updateCount = 0;

        if (isset($data["idType"])) {
            try {
                $user->setRole(Role::from($data["idType"]));
                $updateCount++;
            } catch (\ValueError $e) {
                $this->call(400, "bad_request", "Tipo de usuário inválido", "error")->back();
                return;
            }
        }

        if (isset($data["name"])) {
            if (empty($data["name"])) {
                $this->call(400, "bad_request", "Nome não pode ser vazio", "error")->back();
                return;
            }
            $user->setName($data["name"]);
            $updateCount++;
        }

        if (isset($data["username"])) {
            if (empty($data["username"])) {
                $this->call(400, "bad_request", "O campo username não pode estar em branco", "error")->back();
                return;
            }

            $existingUser = new User();
            if ($existingUser->findByUsername($data["username"]) && $existingUser->getId() !== $user->getId()) {
                $this->call(400, "bad_request", "O username '{$data["username"]}' já está sendo usado por outro usuário", "error")->back();
                return;
            }

            $user->setUsername($data["username"]);
            $updateCount++;
        }

        if (isset($data["email"])) {
            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                $this->call(400, "bad_request", "Email inválido", "error")->back();
                return;
            }
            $user->setEmail($data["email"]);
            $updateCount++;
        }

        if (isset($data["password"])) {
            if (empty($data["password"])) {
                $this->call(400, "bad_request", "Senha não pode ser vazia", "error")->back();
                return;
            }
            $user->setPassword(password_hash($data["password"], PASSWORD_DEFAULT));
            $updateCount++;
        }

        if (isset($data["photo"])) {
            $user->setPhoto($data["photo"]);
            $updateCount++;
        }

        if (isset($data["bio"])) {
            if (empty($data["bio"])) {
                $this->call(400, "bad_request", "Biografia não pode estar em branco!", "error")->back();
                return;
            }
            $user->setBio($data["bio"]);
            $updateCount++;
        }

        if ($updateCount === 0) {
            $this->call(400, "bad_request", "Nenhum campo válido para atualização", "error")->back();
            return;
        }

        if (!$user->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao atualizar usuário: " . $user->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $user->getId(),
            "idType" => $user->getRole()?->value,
            "name" => $user->getName(),
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "photo" => $user->getPhoto(),
            "bio" => $user->getBio(),
            "deleted" => $user->getDeleted()
        ];

        $this->call(200, "success", "Usuário atualizado com sucesso", "success")->back($response);
    }


    public function login(array $data = []): void
    {
        if (empty($data)) {
            $data = $this->getRequestData();
        }

        error_log("Dados recebidos no login: " . print_r($data, true));

        if (empty($data["user"]) || empty($data["password"])) {
            $this->call(400, "bad_request", "Credenciais inválidas", "error")->back();
            return;
        }

        $user = new User();
        $found = $user->findByUsername($data["user"]);

        if (!$found) {
            $found = $user->findByEmail($data["user"]);
        }

        if (!$found) {
            $this->call(401, "unauthorized", "Usuário não encontrado", "error")->back();
            return;
        }

        if (!password_verify($data["password"], $user->getPassword())) {
            $this->call(401, "unauthorized", "Senha inválida", "error")->back();
            return;
        }

        if ($user->getDeleted() === true) {
            $user->setDeleted(false);

            if (!$user->updateById()) {
                $this->call(500, "internal_server_error", "Erro ao reativar o usuário", "error")->back();
                return;
            }

            $this->call(200, "success", "Usuário reativado com sucesso", "success")->back();
            return;
        }

        $jwt = new JWTToken();
        $token = $jwt->create([
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "name" => $user->getName()
        ]);

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

    public function getLoggedUser(): void
    {
        $this->auth();
        $user = new User();

        if (!$user->findById($this->userAuth->id)) {
            $this->call(404, "not_found", "Usuário não encontrado", "error")->back();
            return;
        }

        $response = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "photo" => "http://localhost/ensaiei-main" . IMAGE_DIR . $user->getPhoto() ?? "../assets/images/default-profile.png",
            "bio" => $user->getBio() ?? "Eu amo teatro!"
        ];

        $this->call(200, "success", "Perfil carregado com sucesso", "success")->back($response);
    }
}