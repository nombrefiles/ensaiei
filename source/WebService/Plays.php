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
        $data["actors"] = explode(",", $data["actors"]);

        if (empty($data["name"]) || empty($data["genre"]) || empty($data["script"]) || empty($data["directorId"]) || empty($data["actors"])) {
            $this->call(400, "bad_request", "Todos os campos são obrigatórios", "error")->back();
            return;
        }



        if (!is_array($data['actors'])) {
            $this->call(400, "bad_request", "Campo 'actors' deve ser um array válido", "error")->back();
            return;
        }

        $play = new Play(
            null,
            $data["name"],
            $data["genre"],
            $data["script"],
            null,
            $data["directorId"],
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
                    $actors[] = $actor->getName();
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

    public function updatePlay (array $data): void
    {
        $this->auth();
        var_dump($data);
        var_dump( $this->userAuth);
        var_dump($this->userAuth->name, $this->userAuth->email);
    }

}