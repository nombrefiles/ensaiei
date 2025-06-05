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
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
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
            "location" => $event->getLocation(),
            "longitude" => $event->getLongitude(),
            "latitude" => $event->getLatitude(),
            "organizer" => $event->getOrganizerId(),
            "attractions" => $event->getAttractions(),
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
            || empty($data["startDate"]) || empty($data["endDate"]) || empty($data["startTime"]) || empty($data["endTime"])) {
            $this->call(400, "bad_request", "Todos os campos são obrigatórios", "error")->back();
            return;
        }

        $event = new Event();

        $event->setStartDatetime($data["startDate"], $data["startTime"] ?? null);
        $event->setEndDatetime($data["endDate"], $data["endTime"] ?? null);

        if ($event->getStartDatetime() >= $event->getEndDatetime()) {
            $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
            return;
        }

        $event->setTitle($data["title"]);
        $event->setDescription($data["description"]);
        $event->setLocation($data["location"]);
        $event->setStartDatetime($data["startDate"], $data["startTime"] ?? null);
        $event->setEndDatetime($data["endDate"], $data["endTime"] ?? null);
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

        $event = new Event();
        if (!$event->findById($data["id"])) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        if ($this->userAuth->id !== $event->getOrganizerId()) {
            $this->call(403, "forbidden", "Você não tem permissão para atualizar esse evento", "error")->back();
            return;
        }

        // Atualiza campos básicos se fornecidos
        if (isset($data["title"])) {
            if (empty($data["title"])) {
                $this->call(400, "bad_request", "Título do evento não pode ser vazio", "error")->back();
                return;
            }
            $event->setTitle($data["title"]);
        }

        if (isset($data["location"])) {
            if (empty($data["location"])) {
                $this->call(400, "bad_request", "Local do evento não pode ser vazio", "error")->back();
                return;
            }
            $event->setLocation($data["location"]);
        }

        if (isset($data["description"])) {
            if (empty($data["description"])) {
                $this->call(400, "bad_request", "Descrição do evento não pode ser vazio", "error")->back();
                return;
            }
            $event->setDescription($data["description"]);
        }

        // Lógica de atualização das datas
        $currentStartDatetime = $event->getStartDatetime();
        $currentEndDatetime = $event->getEndDatetime();

        // Atualiza data/hora de início se fornecidos
        if (isset($data["startDate"]) && isset($data["startTime"])) {
            $event->setStartDatetime($data["startDate"], $data["startTime"]);
            if (!$event->getStartDatetime()) {
                $this->call(400, "bad_request", "Data/hora de início inválida", "error")->back();
                return;
            }
        }

        // Atualiza data/hora de término se fornecidos
        if (isset($data["endDate"]) && isset($data["endTime"])) {
            $event->setEndDatetime($data["endDate"], $data["endTime"]);
            if (!$event->getEndDatetime()) {
                $this->call(400, "bad_request", "Data/hora de término inválida", "error")->back();
                return;
            }
        }

        // Validação final das datas
        if ($event->getStartDatetime() >= $event->getEndDatetime()) {
            // Restaura as datas originais em caso de erro
            $event->setStartDatetime($currentStartDatetime);
            $event->setEndDatetime($currentEndDatetime);
            $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
            return;
        }

        if (!$event->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao atualizar evento: " . $event->getErrorMessage(), "error")->back();
            return;
        }

        // Recarrega o evento para garantir que temos os dados atualizados
        $event->findById($data["id"]);

        $response = [
            "id" => $event->getId(),
            "title" => $event->getTitle(),
            "description" => $event->getDescription(),
            "location" => $event->getLocation(),
            "startDate" => $event->getStartDate(),
            "startTime" => $event->getStartTime(),
            "endDate" => $event->getEndDate(),
            "endTime" => $event->getEndTime(),
            "organizerId" => $event->getOrganizerId()
        ];

        $this->call(200, "success", "Evento atualizado com sucesso", "success")->back($response);
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

        $this->call(200, "success", "Evento deletado com sucesso", "success")->back($response);
    }

}