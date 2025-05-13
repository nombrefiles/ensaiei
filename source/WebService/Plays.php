<?php

namespace Source\WebService;

use Source\Models\Play;

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

        // verifica se os dados estão preenchidos
        if(in_array("", $data)){
            $this->call(400, "bad_request", "Dados inválidos", "error")->back();
            return;
        }

        $play = new Play(
            null,
            $data["name"] ?? null,
            $data["genre"] ?? null,
            $data["script"] ?? null,
            $data["costumes"] ?? null,
            $data["director"] ?? null,
            $data["actors"] ?? null
        );

        if(!$play->insert()){
            $this->call(500, "internal_server_error", $play->getErrorMessage(), "error")->back();
            return;
        }
        // montar $response com as informações necessárias para mostrar no front
        $response = [
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
        $response = [
            "name" => $play->getName(),
            "director" => $play->getDirector()->getName()
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