<?php

namespace Source\WebService;

use Source\Models\Actor;

class Actors extends Api
{
    public function listActors (): void
    {
        $actor = new Actor();
        $this->call(200, "success", "Lista de peças", "success")
            ->back($actor->findAll());
    }

    public function createActors(array $data)
    {

        // verifica se os dados estão preenchidos
        if(in_array("", $data)){
            $this->call(400, "bad_request", "Dados inválidos", "error")->back();
            return;
        }

        $actor = new Actor(
            null,
            $data["name"] ?? null,
            $data["plays"] ?? null,

        );

        if(!$actor->insert()){
            $this->call(500, "internal_server_error", $actor->getErrorMessage(), "error")->back();
            return;
        }
        // montar $response com as informações necessárias para mostrar no front
        $response = [
            "name" => $actor->getName(),
            "play" => $actor->getPlays(),
        ];

        $this->call(201, "created", "Ator criada com sucesso", "success")
            ->back($response);

    }

    public function listActorById (array $data): void
    {

        if(!isset($data["id"])) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        if(!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $actor = new Actor();
        if(!$actor->findById($data["id"])){
            $this->call(200, "error", "Ator não encontrado", "error")->back();
            return;
        }
        $response = [
            "name" => $actor->getName(),
            "plays" => $actor->getPlays()
        ];
        $this->call(200, "success", "Encontrado com sucesso", "success")->back($response);
    }

    public function updateActor (array $data): void
    {
        $this->auth();
        var_dump($data);
        var_dump( $this->userAuth);
        var_dump($this->userAuth->name, $this->userAuth->email);
    }

}