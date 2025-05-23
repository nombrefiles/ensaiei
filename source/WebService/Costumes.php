<?php

namespace Source\WebService;

use Source\Models\costume;

class Costumes extends Api
{
    public function listCostumes (): void
    {
        $costume = new Costume();
        $this->call(200, "success", "Lista de Figurinos", "success")
            ->back($costume->findAll());
    }

    public function createCostume(array $data)
    {

        if (empty($data["description"]) || empty($data["playId"]) ) {
            $this->call(400, "bad_request", "Todos os campos são obrigatórios", "error")->back();
            return;
        }


        $costume = new Costume(
            null,
            $data["description"],
            $data["playId"],
        );


        $response = [
            "id" => $costume->getId(),
            "description" => $costume->getDescription(),
            "playId" => $costume->getPlayId()
        ];

        $this->call(201, "created", "Figurino criado com sucesso", "success")
            ->back($response);
    }

    public function listCostumeById (array $data): void
    {

        if(!isset($data["id"])) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        if(!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $costume = new Costume();
        if(!$costume->findById($data["id"])){
            $this->call(200, "error", "Peça não encontrada", "error")->back();
            return;
        }
        $response = [
            "name" => $costume->getId(),
            "description" => $costume->getDescription()
        ];
        $this->call(200, "success", "Encontrado com sucesso", "success")->back($response);
    }

    public function updateCostume (array $data): void
    {
        $this->auth();
        var_dump($data);
        var_dump( $this->userAuth);
        var_dump($this->userAuth->name, $this->userAuth->email);
    }

}