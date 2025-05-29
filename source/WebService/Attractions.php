<?php

namespace Source\WebService;

use Source\enums\Type;
use Source\Models\Attraction;
use Source\Models\User;
use ValueError;

class Attractions extends Api
{
    public function listAttractions(): void
    {
        $attraction = new Attraction();
        $this->call(200, "success", "Lista de atrações", "success")
            ->back($attraction->findAll());
    }

    public function listAttractionById(array $data): void
    {
        if (!isset($data["id"])) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        if (!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $attraction = new Attraction();
        if (!$attraction->findById($data["id"])) {
            $this->call(404, "not_found", "Atração não encontrada", "error")->back();
            return;
        }

        $performers = [];
        foreach ($attraction->getPerformers() as $performerId) {
            $performer = new User();
            if ($performer->findById($performerId)) {
                $performers[] = [
                    'id' => $performer->getId(),
                    'name' => $performer->getName()
                ];
            }
        }

        $response = [
            "id" => $attraction->getId(),
            "name" => $attraction->getName(),
            "type" => $attraction->getType(),
            "startDatetime" => $attraction->getStartDatetime(),
            "endDatetime" => $attraction->getEndDatetime(),
            "specificLocation" => $attraction->getSpecificLocation(),
            "performers" => $performers,
        ];
        $this->call(200, "success", "Encontrado com sucesso", "success")->back($response);
    }

    public function createAttraction(array $data): void
    {
        $this->auth();

        if (empty($data["name"]) || empty($data["startDatetime"]) || empty($data["endDatetime"]) || empty($data["specificLocation"])) {
            $this->call(400, "bad_request", "Todos os campos são obrigatórios", "error")->back();
            return;
        }

        $start = strtotime($data["startDatetime"]);
        $end = strtotime($data["endDatetime"]);
        if ($start >= $end) {
            $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
            return;
        }

        try {
            $type = isset($data["type"]) ? Type::from($data["type"]) : Type::OTHER;
        } catch (ValueError $e) {
            $this->call(400, "bad_request", "Tipo de atração inválido", "error")->back();
            return;
        }

        if (empty($data["performers"])) {
            $data["performers"] = [$this->userAuth->id];
        } else {
            if (is_string($data["performers"])) {
                $data["performers"] = explode(",", $data["performers"]);
            }

            if (!is_array($data["performers"])) {
                $this->call(400, "bad_request", "Campo 'performers' deve ser um array ou string separada por vírgulas", "error")->back();
                return;
            }

            $data["performers"][] = $this->userAuth->id;
        }

        if (!$this->userAuth) {
            $this->call(401, "unauthorized", "Usuário não autenticado", "error")->back();
            return;
        }

        $attraction = new Attraction(
            null,
            $data["name"],
            $type,
            $data["eventId"],
            $data["startDatetime"],
            $data["endDatetime"],
            $data["specificLocation"],
            $data["performers"],
            false
        );

        if (!$attraction->insertWithPerformers()) {
            $this->call(500, "internal_server_error", $attraction->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $attraction->getId(),
            "name" => $attraction->getName(),
            "performers" => $attraction->getPerformers()
        ];

        $this->call(201, "created", "Atração criada com sucesso", "success")
            ->back($response);
    }

    public function updateAttraction(array $data): void
    {
        $this->auth();

        if (!isset($data["id"])) {
            $this->call(400, "bad_request", "ID da atração não fornecido", "error")->back();
            return;
        }

        if (!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $attraction = new Attraction();
        if (!$attraction->findById($data["id"])) {
            $this->call(404, "not_found", "Atração não encontrada", "error")->back();
            return;
        }

        if (!in_array($this->userAuth->id, $attraction->getPerformers())) {
            $this->call(403, "forbidden", "Você não tem permissão para atualizar essa atração", "error")->back();
            return;
        }

        if (isset($data["name"]) && empty($data["name"])) {
            $this->call(400, "bad_request", "Nome da atração não pode ser vazio", "error")->back();
            return;
        }

        if (isset($data["type"])) {
            try {
                $type = Type::from($data["type"]);
                $attraction->setType($type);
            } catch (ValueError $e) {
                $this->call(400, "bad_request", "Tipo de atração inválido", "error")->back();
                return;
            }
        }

        if (isset($data["startDatetime"]) && isset($data["endDatetime"])) {
            $start = strtotime($data["startDatetime"]);
            $end = strtotime($data["endDatetime"]);
            if ($start >= $end) {
                $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
                return;
            }
        }

        if (isset($data["name"])) {
            $attraction->setName($data["name"]);
        }
        if (isset($data["startDatetime"])) {
            $attraction->setStartDatetime($data["startDatetime"]);
        }
        if (isset($data["endDatetime"])) {
            $attraction->setEndDatetime($data["endDatetime"]);
        }
        if (isset($data["specificLocation"])) {
            $attraction->setSpecificLocation($data["specificLocation"]);
        }

        if (isset($data["performers"])) {
            if (is_string($data["performers"])) {
                $data["performers"] = explode(",", $data["performers"]);
            }
            $attraction->setPerformers($data["performers"]);

            if (!$attraction->updateWithPerformers()) {
                $this->call(500, "internal_server_error", $attraction->getErrorMessage(), "error")->back();
                return;
            }
        }

        $performersBackup = $attraction->getPerformers();
        $attraction->setPerformers(null);

        if (!$attraction->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao atualizar atração: " . $attraction->getErrorMessage(), "error")->back();
            return;
        }

        $attraction->setPerformers($performersBackup);
        $attraction->findById($data["id"]);

        $performers = [];
        foreach ($attraction->getPerformers() as $performerId) {
            $performer = new User();
            if ($performer->findById($performerId)) {
                $performers[] = [
                    'id' => $performer->getId(),
                    'name' => $performer->getName()
                ];
            }
        }

        $response = [
            "id" => $attraction->getId(),
            "name" => $attraction->getName(),
            "type" => $attraction->getType(),
            "eventId" => $attraction->getEventId(),
            "startDatetime" => $attraction->getStartDatetime(),
            "endDatetime" => $attraction->getEndDatetime(),
            "specificLocation" => $attraction->getSpecificLocation(),
            "performers" => $performers,
        ];

        $this->call(200, "success", "Atração atualizada com sucesso", "success")->back($response);
    }

    public function deleteAttraction(array $data): void
    {
        $this->auth();

        if (!isset($data['id'])) {
            $this->call(400, "bad_request", "ID da atração não fornecido", "error")->back();
            return;
        }

        if (!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $attraction = new Attraction();
        if (!$attraction->findById($data['id'])) {
            $this->call(404, 'not_found', 'Atração não encontrada', "error")->back();
            return;
        }

        if (!in_array($this->userAuth->id, $attraction->getPerformers())) {
            $this->call(403, "forbidden", "Você não tem permissão para deletar essa atração", "error")->back();
            return;
        }

        $attraction->setDeleted(true);

        if (!$attraction->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao deletar a atração", "error")->back();
            return;
        }

        $response = [
            "id" => $attraction->getId(),
            "name" => $attraction->getName(),
        ];

        $this->call(200, "success", "Atração deletada com sucesso", "success")->back($response);
    }

public function listAttractionsByEvent(array $data): void 
{
    if (!isset($data["eventId"])) {
        $this->call(400, "bad_request", "ID do evento não fornecido", "error")->back();
        return;
    }

    if (!filter_var($data["eventId"], FILTER_VALIDATE_INT)) {
        $this->call(400, "bad_request", "ID do evento inválido", "error")->back();
        return;
    }

    $attraction = new Attraction();
    $stmt = Connect::getInstance()->prepare("
        SELECT * FROM attractions 
        WHERE eventId = :eventId AND deleted = false
    ");
    $stmt->bindParam(":eventId", $data["eventId"]);
    $stmt->execute();
    
    $attractions = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $attraction = new Attraction();
        $attraction->findById($row["id"]);
        
        $performers = [];
        foreach ($attraction->getPerformers() as $performerId) {
            $performer = new User();
            if ($performer->findById($performerId)) {
                $performers[] = [
                    'id' => $performer->getId(),
                    'name' => $performer->getName()
                ];
            }
        }

        $attractions[] = [
            "id" => $attraction->getId(),
            "name" => $attraction->getName(),
            "type" => $attraction->getType(),
            "startDatetime" => $attraction->getStartDatetime(),
            "endDatetime" => $attraction->getEndDatetime(),
            "specificLocation" => $attraction->getSpecificLocation(),
            "performers" => $performers
        ];
    }

    $this->call(
        200, 
        "success", 
        "Lista de atrações do evento recuperada com sucesso", 
        "success"
    )->back($attractions);
}
}