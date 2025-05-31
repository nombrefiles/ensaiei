<?php

namespace Source\WebService;

use Source\Models\Attraction;
use Source\Models\Event;
use Source\Models\User;

class Events extends Api
{
    public function listEvents(): void
    {
        $event = new Event();
        $this->call(200, "success", "Lista de eventos", "success")
            ->back($event->findAll());
    }

    public function listEventById(array $data): void
    {
        if (!isset($data["id"])) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        if (!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($data["id"])) {
            $this->call(404, "not_found", "Evento não encontrada", "error")->back();
            return;
        }

        $event = new Event();
        $event->findById($data['id']);
        $attractions = $event->getAttractions();

        foreach ($attractions as $attractionId) {
            $attraction = new Attraction();
            if ($attraction->findById($attractionId)) {
                $attractions[] = [
                    'id' => $attraction->getId(),
                    'name' => $attraction->getName(),
                    'type' => $attraction->getType(),
                ];
            }
        }

        $user = new User();

        $response = [
            "id" => $event->getId(),
            "title" => $event->getTitle(),
            "description" => $event->getDescription(),
            "startDatetime" => $event->getStartDatetime(),
            "endDatetime" => $event->getEndDatetime(),
            "location" => $event->getLocation(),
            "longitude" => $event->getLongitude(),
            "latitude" => $event->getLatitude(),
            "organizer" => $event->getOrganizerId(),
            "attractions" => $attractions,
        ];
        $this->call(200, "success", "Encontrado com sucesso", "success")->back($response);
    }

    public function createEvent(array $data): void
    {
        $this->auth();

        if (!$this->userAuth) {
            $this->call(401, "unauthorized", "Usuário não autenticado", "error")->back();
            return;
        }

        if (empty($data["title"]) || empty($data["description"]) || empty($data["location"]) 
            || empty($data["startDatetime"]) || empty($data["endDatetime"]) || empty($data["organizerId"])) {
            $this->call(400, "bad_request", "Todos os campos são obrigatórios", "error")->back();
            return;
        }

        $start = strtotime($data["startDatetime"]);
        $end = strtotime($data["endDatetime"]);
        if ($start >= $end) {
            $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
            return;
        }

        $event = new Event();
        $event->setTitle($data["title"]);
        $event->setDescription($data["description"]);
        $event->setLocation($data["location"]);
        $event->setStartDatetime($data["startDatetime"]);
        $event->setEndDatetime($data["endDatetime"]);
        $event->setOrganizerId($data["organizerId"]);

        if (!$event->insert()) {
            $this->call(500, "internal_server_error", "Erro ao criar evento", "error")->back();
            return;
        }

        $response = [
            "id" => $event->getId(),
            "title" => $event->getTitle(),
            "description" => $event->getDescription(),
            "startDatetime" => $event->getStartDatetime(),
            "endDatetime" => $event->getEndDatetime(),
            "location" => $event->getLocation(),
            "organizerId" => $event->getOrganizerId(),
            "attractions" => $event->getAttractions()
        ];

        $this->call(201, "created", "Evento criado com sucesso", "success")
            ->back($response);
    }

    public function updateEvent(array $data): void
    {
        $this->auth();

        if (!isset($data["id"])) {
            $this->call(400, "bad_request", "ID do evento não fornecido", "error")->back();
            return;
        }

        if (!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($data["id"])) {
            $this->call(404, "not_found", "Evento não encontrada", "error")->back();
            return;
        }

        if ($this->userAuth->id !== $event->getOrganizerId()) {
            $this->call(403, "forbidden", "Você não tem permissão para atualizar essa atração", "error")->back();
            return;
        }

        if (isset($data["title"]) && empty($data["title"])) {
            $this->call(400, "bad_request", "Título do evento não pode ser vazio", "error")->back();
            return;
        }

        if (isset($data["location"]) && empty($data["location"])) {
            $this->call(400, "bad_request", "Local do evento não pode ser vazio", "error")->back();
            return;
        }

        if (isset($data["description"]) && empty($data["description"])) {
            $this->call(400, "bad_request", "Descrição do evento não pode ser vazio", "error")->back();
            return;
        }



        if (isset($data["startDatetime"])) {
            $event->setStartDatetime($data["startDatetime"]);
        }
        if (isset($data["endDatetime"])) {
            $event->setEndDatetime($data["endDatetime"]);
        }
        if (isset($data["startDatetime"]) && isset($data["endDatetime"])) {
            $start = strtotime($data["startDatetime"]);
            $end = strtotime($data["endDatetime"]);
            if ($start >= $end) {
                $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
                return;
            }
        }

        if (isset($data["attractions"])) {
            if (is_string($data["attractions"])) {
                $data["attractions"] = explode(",", $data["attractions"]);
            }
            $event->setAttractions($data["attractions"]);
        }

        $attractionsBackup = $event->getAttractions();
        $event->setAttractions([]);

        if (!$event->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao atualizar evento: " . $event->getErrorMessage(), "error")->back();
            return;
        }

        $event->setAttractions($attractionsBackup);
        $event->findById($data["id"]);

        $attractions = [];
        foreach ($event->getAttractions() as $attractionId) {
            $attraction = new Attraction();
            if ($attraction->findById($attractionId)) {
                $attractions[] = [
                    'id' => $attraction->getId(),
                    'name' => $attraction->getName()
                ];
            }
        }

        $response = [
            "id" => $event->getId(),
            "title" => $event->getTitle(),
            "description" => $event->getDescription(),
            "location" => $event->getLocation(),
            "startDatetime" => $event->getStartDatetime(),
            "endDatetime" => $event->getEndDatetime(),
            "organizerId" => $event->getOrganizerId(),
            "attractions" => $attractions,
        ];

        $this->call(200, "success", "Evento atualizada com sucesso", "success")->back($response);
    }


    public function deleteEvent(array $data): void
    {
        $this->auth();

        if (!isset($data['id'])) {
            $this->call(400, "bad_request", "ID do evento não fornecido", "error")->back();
            return;
        }

        if (!filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($data['id'])) {
            $this->call(404, 'not_found', 'Evento não encontrado', "error")->back();
            return;
        }
        if ($this->userAuth->id != $event->getOrganizerId()) {
            $this->call(403, "forbidden", "Você não tem permissão para deletar esse evento", "error")->back();
            return;
        }

        $event->setDeleted(true);

        if (!$event->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao deletar o evento", "error")->back();
            return;
        }

        $response = [
            "id" => $event->getId(),
            "title" => $event->getTitle(),
        ];

        $this->call(200, "success", "Atração deletada com sucesso", "success")->back($response);
    }

}