<?php

namespace Source\WebService;

use Source\Models\Attraction;
use Source\Models\Event;

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
        if (empty($data["id"]) || !filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($data["id"])) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        $attractions = [];
        foreach ($event->getAttractions() as $attr) {
            $attractions[] = [
                'id' => $attr['id'],
                'name' => $attr['name'],
                'type' => $attr['type']
            ];
        }

        $response = [
            "id" => $event->getId(),
            "title" => $event->getTitle(),
            "description" => $event->getDescription(),
            "startDatetime" => $event->getStartDatetime()->format('Y-m-d H:i:s'),
            "endDatetime" => $event->getEndDatetime()->format('Y-m-d H:i:s'),
            "location" => $event->getLocation(),
            "longitude" => $event->getLongitude(),
            "latitude" => $event->getLatitude(),
            "organizer" => $event->getOrganizerId(),
            "attractions" => $attractions,
        ];
        $this->call(200, "success", "Evento encontrado com sucesso", "success")->back($response);
    }

    public function createEvent(array $data): void
    {
        $this->auth();
        if (!$this->userAuth) {
            $this->call(401, "unauthorized", "Usuário não autenticado", "error")->back();
            return;
        }

        if (empty($data["title"]) || empty($data["description"]) || empty($data["location"]) ||
            empty($data["startDate"]) || empty($data["endDate"]) || empty($data["startTime"]) || empty($data["endTime"])) {
            $this->call(400, "bad_request", "Todos os campos são obrigatórios", "error")->back();
            return;
        }

        $event = new Event();
        $event->setStartDatetime($data["startDate"], $data["startTime"]);
        $event->setEndDatetime($data["endDate"], $data["endTime"]);

        if ($event->getStartDatetime() >= $event->getEndDatetime()) {
            $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
            return;
        }

        $event->setTitle($data["title"]);
        $event->setDescription($data["description"]);
        $event->setLocation($data["location"]);
        $event->setOrganizerId($this->userAuth->id);

        if (!$event->insert()) {
            $this->call(500, "internal_server_error", "Erro ao criar evento", "error")->back();
            return;
        }

        $response = [
            "id" => $event->getId(),
            "title" => $event->getTitle(),
            "description" => $event->getDescription(),
            "startDate" => $event->getStartDate(),
            "startTime" => $event->getStartTime(),
            "endDate" => $event->getEndDate(),
            "endTime" => $event->getEndTime(),
            "startDatetime" => $event->getStartDatetime()->format('Y-m-d H:i:s'),
            "endDatetime" => $event->getEndDatetime()->format('Y-m-d H:i:s'),
            "location" => $event->getLocation(),
            "organizerId" => $event->getOrganizerId(),
            "attractions" => []
        ];

        $this->call(201, "created", "Evento criado com sucesso", "success")->back($response);
    }

    public function updateEvent(array $data): void
    {
        $this->auth();

        if (empty($data["id"]) || !filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do evento inválido", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($data["id"])) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        if ($this->userAuth->id !== $event->getOrganizerId()) {
            $this->call(403, "forbidden", "Você não tem permissão para atualizar esse evento", "error")->back();
            return;
        }

        if (isset($data["title"])) $event->setTitle($data["title"]);
        if (isset($data["description"])) $event->setDescription($data["description"]);
        if (isset($data["location"])) $event->setLocation($data["location"]);
        if (isset($data["startDatetime"])) $event->setStartDatetime($data["startDatetime"]);
        if (isset($data["endDatetime"])) $event->setEndDatetime($data["endDatetime"]);

        if (isset($data["startDatetime"]) && isset($data["endDatetime"])) {
            if (strtotime($data["startDatetime"]) >= strtotime($data["endDatetime"])) {
                $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
                return;
            }
        }

        if (isset($data["attractions"])) {
            $event->setAttractions(is_array($data["attractions"]) ? $data["attractions"] : explode(",", $data["attractions"]));
        }

        $attractionsBackup = $event->getAttractions();
        $event->setAttractions([]);

        if (!$event->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao atualizar evento", "error")->back();
            return;
        }

        $event->setAttractions($attractionsBackup);
        $event->findById($data["id"]);

        $attractions = [];
        foreach ($event->getAttractions() as $attr) {
            $attractions[] = [
                'id' => $attr['id'],
                'name' => $attr['name'],
                'type' => $attr['type'] ?? null
            ];
        }

        $response = [
            "id" => $event->getId(),
            "title" => $event->getTitle(),
            "description" => $event->getDescription(),
            "location" => $event->getLocation(),
            "startDatetime" => $event->getStartDatetime()->format('Y-m-d H:i:s'),
            "endDatetime" => $event->getEndDatetime()->format('Y-m-d H:i:s'),
            "organizerId" => $event->getOrganizerId(),
            "attractions" => $attractions,
        ];

        $this->call(200, "success", "Evento atualizado com sucesso", "success")->back($response);
    }

    public function deleteEvent(array $data): void
    {
        $this->auth();

        if (empty($data["id"]) || !filter_var($data["id"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID inválido", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($data['id'])) {
            $this->call(404, 'not_found', 'Evento não encontrado', "error")->back();
            return;
        }

        if ($this->userAuth->id !== $event->getOrganizerId()) {
            $this->call(403, "forbidden", "Você não tem permissão para deletar esse evento", "error")->back();
            return;
        }

        $event->setDeleted(true);

        if (!$event->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao deletar o evento", "error")->back();
            return;
        }

        $this->call(200, "success", "Evento deletado com sucesso", "success")
            ->back(["id" => $event->getId(), "title" => $event->getTitle()]);
    }
}
