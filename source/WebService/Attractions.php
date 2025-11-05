<?php

namespace Source\WebService;

use Source\Enums\Type;
use Source\Models\Attraction;
use Source\Models\Event;
use Source\Models\User;
use ValueError;

class Attractions extends Api
{

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
            "date" => $attraction->getStartDate(),
            "startTime" => $attraction->getStartTime(),
            "endTime" => $attraction->getEndTime(),
            "specificLocation" => $attraction->getSpecificLocation(),
            "performers" => $performers,
        ];
        $this->call(200, "success", "Encontrado com sucesso", "success")->back($response);
    }

    public function createAttraction(array $data): void
    {
        $this->auth();

        if (empty($data["name"]) || empty($data["date"]) || empty($data["startTime"]) || empty($data["endTime"]) || empty($data["specificLocation"])) {
            $this->call(400, "bad_request", "Todos os campos são obrigatórios", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($data["eventId"])) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        try {
            $type = isset($data["type"]) ? Type::from($data["type"]) : Type::OTHER;
        } catch (ValueError $e) {
            $this->call(400, "bad_request", "Tipo de atração inválido", "error")->back();
            return;
        }

        $attraction = new Attraction();
        $attraction->setName($data["name"]);
        $attraction->setType($type);
        $attraction->setEventId($data["eventId"]);
        $attraction->setStartDatetime($data["date"], $data["startTime"]);
        $attraction->setEndDatetime($data["date"], $data["endTime"]);
        $attraction->setSpecificLocation($data["specificLocation"]);

        if ($attraction->getStartDatetime() < $event->getStartDatetime() || 
            $attraction->getEndDatetime() > $event->getEndDatetime()) {
            $this->call(400, "bad_request", "A atração deve ocorrer dentro do período do evento", "error")->back();
            return;
        }

        if ($attraction->getStartDatetime() >= $attraction->getEndDatetime()) {
            $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
            return;
        }

        $performerIds = [$this->userAuth->id];

        if (!empty($data["performers"])) {
            $performerUsernames = is_string($data["performers"]) ? 
                explode(",", $data["performers"]) : 
                $data["performers"];

            foreach ($performerUsernames as $username) {
                $performer = new User();
                if ($performer->findByUsername(trim($username))) {
                    $performerIds[] = $performer->getId();
                } else {
                    $this->call(400, "bad_request", "Usuário não encontrado: " . $username, "error")->back();
                    return;
                }
            }
        }

        $attraction->setPerformers(array_unique($performerIds));

        if (!$attraction->insertWithPerformers()) {
            $this->call(500, "internal_server_error", $attraction->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $attraction->getId(),
            "name" => $attraction->getName(),
            "date" => $attraction->getStartDate(),
            "startTime" => $attraction->getStartTime(),
            "endTime" => $attraction->getEndTime(),
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

        $attraction = new Attraction();
        if (!$attraction->findById($data["id"])) {
            $this->call(404, "not_found", "Atração não encontrada", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($attraction->getEventId())) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        if (isset($data["name"])) {
            $attraction->setName($data["name"]);
        }
        if (isset($data["specificLocation"])) {
            $attraction->setSpecificLocation($data["specificLocation"]);
        }

        $startDate = $data["date"] ?? $attraction->getStartDate();
        $startTime = $data["startTime"] ?? $attraction->getStartTime();
        $endTime = $data["endTime"] ?? $attraction->getEndTime();
        
        $attraction->setStartDatetime($startDate, $startTime);
        $attraction->setEndDatetime($startDate, $endTime);

        if ($attraction->getStartDatetime() >= $attraction->getEndDatetime()) {
            $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
            return;
        }

        if ($attraction->getStartDatetime() < $event->getStartDatetime() ||
            $attraction->getEndDatetime() > $event->getEndDatetime()) {
            $this->call(400, "bad_request", "A atração deve ocorrer dentro do período do evento", "error")->back();
            return;
        }

        $performersData = null;
        if (isset($data["performers"])) {
            $performersData = $data["performers"];
        } elseif (isset($data["perfomers"])) {
            $performersData = $data["perfomers"];
        }

        if ($performersData !== null) {
            error_log("Performers recebidos: " . print_r($performersData, true));
            
            $performers = is_string($performersData) 
                ? array_map('trim', explode(",", $performersData)) 
                : $performersData;

            error_log("Performers após processamento: " . print_r($performers, true));

            $validPerformers = [];
            foreach ($performers as $username) {
                $performer = new User();
                if ($performer->findByUsername($username)) {
                    $validPerformers[] = $performer->getId();
                    error_log("Performer encontrado: {$username} -> ID: {$performer->getId()}");
                } else {
                    error_log("Performer não encontrado: {$username}");
                    $this->call(400, "bad_request", "Usuário não encontrado: " . $username, "error")->back();
                    return;
                }
            }

            error_log("Performers válidos: " . print_r($validPerformers, true));
            $attraction->setPerformers($validPerformers);
        }

        if (!$attraction->updateWithPerformers()) {
            $this->call(500, "internal_server_error", "Erro ao atualizar atração: " . $attraction->getErrorMessage(), "error")->back();
            return;
        }

        $attraction->findById($data["id"]);

        $performers = [];
        if ($attraction->getPerformers()) {
            foreach ($attraction->getPerformers() as $performerId) {
                $performer = new User();
                if ($performer->findById($performerId)) {
                    $performers[] = [
                        'id' => $performer->getId(),
                        'name' => $performer->getName(),
                        'username' => $performer->getUsername()
                    ];
                }
            }
        }

        $response = [
            "id" => $attraction->getId(),
            "name" => $attraction->getName(),
            "type" => $attraction->getType()->value,
            "eventId" => $attraction->getEventId(),
            "startDatetime" => $attraction->getStartDatetime() ? $attraction->getStartDatetime()->format('Y-m-d H:i:s') : null,
            "endDatetime" => $attraction->getEndDatetime() ? $attraction->getEndDatetime()->format('Y-m-d H:i:s') : null,
            "specificLocation" => $attraction->getSpecificLocation(),
            "performers" => $performers
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

        $event = new Event();
        if (!$event->findById($data["eventId"])) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        $attraction = new Attraction();
        $attractionsList = $attraction->findByEventId($data["eventId"]);


        if (empty($attractionsList)) {
            $responseData = [
                "event" => [
                    "id" => $event->getId(),
                    "title" => $event->getTitle(),
                    "attractions" => "Evento sem nenhuma atração."
            ]
            ];

            $this->call(
                200,
                "success",
                "Lista de atrações do evento recuperada com sucesso",
                "success"
            )->back($responseData);
            return;
        }

        $attractions = [];
        foreach ($attractionsList as $attraction) {
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

        $responseData = [
            "event" => [
                "id" => $event->getId(),
                "title" => $event->getTitle(),
                "attractions" => $attractions
            ]
        ];

        $this->call(
            200,
            "success",
            "Lista de atrações do evento recuperada com sucesso",
            "success"
        )->back($responseData);
    }
}