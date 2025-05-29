<?php

namespace Source\WebService;

use Source\Models\Actor;
use Source\Models\Play;
use Source\Models\User;

class Plays extends Api
{
    public function listPlays (): void
    {
        $play = new Play();
        $this->call(200, "success", "Lista de peças", "success")
            ->back($play->findAll());
    }

    public function createPlay(array $data)
    {
        $this->auth();

        if (empty($data["name"]) || empty($data["genre"]) || empty($data["script"]) || empty($data["actors"])) {
            $this->call(400, "bad_request", "Todos os campos são obrigatórios", "error")->back();
            return;
        }

        $data["actors"] = explode(",", $data["actors"]);
        
        if (!is_array($data['actors'])) {
            $this->call(400, "bad_request", "Campo 'actors' deve ser um array válido", "error")->back();
            return;
        }

        if (!$this->userAuth) {
            $this->call(401, "unauthorized", "Usuário não autenticado", "error")->back();
            return;
        }

        $play = new Play(
            null,
            $data["name"],
            $data["genre"],
            $data["script"],
            $this->userAuth->id,
            $data["actors"]
        );

        if (!$play->insertWithActors()) {
            $this->call(500, "internal_server_error", $play->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $play->getId(),
            "name" => $play->getName(),
            "actors" => $play->getActors()
        ];

        $this->call(201, "created", "Peça criada com sucesso", "success")
            ->back($response);
    }

    public function listPlayById (array $data): void
    {

        if(!isset($data["id"])) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        if(!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $play = new Play();
        if(!$play->findById($data["id"])){
            $this->call(200, "error", "Peça não encontrada", "error")->back();
            return;
        }

        $user = new User();
        $user->findById($play->getDirectorId());

        $actors = [];
        foreach ($play->getActors() as $actorId) {
            $actor = new Actor();
            if ($actor->findById($actorId)) {
                $actors[] = [
                    'id' => $actor->getId(),
                    'name' => $actor->getName()
                ];
            }
        }

        $response = [
            "name" => $play->getName(),
            "genre" => $play->getGenre(),
            "director" => [
                "id" => $user->getId(),
                "username" => $user->getUsername(),
                "name" => $user->getName()
            ],
            "actors" => $actors
        ];
        $this->call(200, "success", "Encontrado com sucesso", "success")->back($response);
    }

    public function updatePlay(array $data): void
    {
        $this->auth();

        if (!isset($data["id"])) {
            $this->call(400, "bad_request", "ID da peça não fornecido", "error")->back();
            return;
        }

        if (!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $play = new Play();
        if (!$play->findById($data["id"])) {
            $this->call(404, "not_found", "Peça não encontrada", "error")->back();
            return;
        }

        if ($this->userAuth->id !== $play->getDirectorId()) {
            $this->call(403, "forbidden", "Você não tem permissão para atualizar essa peça", "error")->back();
            return;
        }

        if (isset($data["name"])) {
            if (empty($data["name"])) {
                $this->call(400, "bad_request", "Nome da peça não pode ser vazio", "error")->back();
                return;
            }
            $play->setName($data["name"]);
        }

        if (isset($data["genre"])) {
            if (empty($data["genre"])) {
                $this->call(400, "bad_request", "Gênero não pode ser vazio", "error")->back();
                return;
            }
            $play->setGenre($data["genre"]);
        }

        if (isset($data["script"])) {
            if (empty($data["script"])) {
                $this->call(400, "bad_request", "Roteiro não pode ser vazio", "error")->back();
                return;
            }
            $play->setScript($data["script"]);
        }

        if (isset($data["actors"])) {
            if (is_string($data["actors"])) {
                $data["actors"] = explode(",", $data["actors"]);
            }
            $play->setActors($data["actors"]);

            if (!$play->updateWithActors()) {
                $this->call(500, "internal_server_error", $play->getErrorMessage(), "error")->back();
                return;
            }
        }

        $actorsBackup = $play->getActors();
        $play->setActors(null);

        if (!$play->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao atualizar peça: " . $play->getErrorMessage(), "error")->back();
            return;
        }

        $play->setActors($actorsBackup);

        $user = new User();

        $play->findById($data["id"]);

        $actors = [];
        foreach ($play->getActors() as $actorId) {
            $actor = new Actor();
            if ($actor->findById($actorId)) {
                $actors[] = [
                    'id' => $actor->getId(),
                    'name' => $actor->getName()
                ];
            }
        }

        $response = [
            "id" => $play->getId(),
            "name" => $play->getName(),
            "genre" => $play->getGenre(),
            "script" => $play->getScript(),
            "directorId" => $this->userAuth->id,
            "actors" => $actors
        ];

        $this->call(200, "success", "Peça atualizada com sucesso", "success")->back($response);
    }

    public function deletePlay(array $data): void {
        $this->auth();

        if(!isset($data['id'])){
            $this->call(400, "bad_request", "ID da peça não fornecido", "error")->back();
            return;
        }

        if (!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $play = new Play();
        if(!$play->findById($data['id'])){
            $this->call(404, 'not_found', 'Peça não encontrada', "error")->back();
            return;
        }

        if($this->userAuth->id !== $play->getDirectorId()){
            $this->call(401, 'forbidden', 'Você não tem autorização para alterar essa peça!', 'error')->back();
            return;
        }

        $play->setDeleted(true);

        if (!$play->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao deletar a peça", "error")->back();
            return;
        }

        $response = [
            "id" => $play->getId(),
            "name" => $play->getName(),
        ];

        $this->call(200, "success", "Peça deletada com sucesso", "success")->back($response);
    }


}