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

    public function listMyEvents(): void
    {
        $this->auth();

        try {
            $stmt = \Source\Core\Connect::getInstance()->prepare("
            SELECT * FROM events 
            WHERE organizerId = :organizerId 
            AND deleted = false
            ORDER BY startDatetime DESC
        ");

            $stmt->bindValue(":organizerId", $this->userAuth->id, \PDO::PARAM_INT);
            $stmt->execute();

            $events = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $this->call(200, "success", "Seus eventos", "success")->back($events);
        } catch (\PDOException $e) {
            $this->call(500, "internal_server_error", "Erro ao buscar eventos: " . $e->getMessage(), "error")->back();
        }
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
            "status" => $event->getStatus(),
            "attractions" => $event->getAttractions(),
        ];
        $this->call(200, "success", "Encontrado com sucesso", "success")->back($response);
    }

    public function createEvent(array $data): void
    {
        $this->auth();

        error_log("===== CREATE EVENT - INICIO =====");
        error_log("Dados recebidos: " . print_r($data, true));
        error_log("User auth: " . print_r($this->userAuth, true));

        if (!$this->userAuth) {
            error_log("ERRO: Usuário não autenticado");
            $this->call(401, "unauthorized", "Usuário não autenticado", "error")->back();
            return;
        }

        if (empty($data["title"])) {
            error_log("ERRO: Título vazio");
            $this->call(400, "bad_request", "Título é obrigatório", "error")->back();
            return;
        }

        if (empty($data["description"])) {
            error_log("ERRO: Descrição vazia");
            $this->call(400, "bad_request", "Descrição é obrigatória", "error")->back();
            return;
        }

        if (empty($data["location"])) {
            error_log("ERRO: Local vazio");
            $this->call(400, "bad_request", "Local é obrigatório", "error")->back();
            return;
        }

        if (empty($data["startDate"]) || empty($data["endDate"]) || empty($data["startTime"]) || empty($data["endTime"])) {
            error_log("ERRO: Datas/horários faltando");
            $this->call(400, "bad_request", "Todos os campos de data e hora são obrigatórios", "error")->back();
            return;
        }

        error_log("Validação de campos OK");

        $event = new Event();

        error_log("Definindo organizerId: " . $this->userAuth->id);
        $event->setOrganizerId($this->userAuth->id);

        error_log("Definindo título: " . $data["title"]);
        $event->setTitle($data["title"]);

        error_log("Definindo descrição: " . $data["description"]);
        $event->setDescription($data["description"]);

        error_log("Definindo local: " . $data["location"]);
        $event->setLocation($data["location"]);

        error_log("Definindo startDatetime: {$data['startDate']} {$data['startTime']}");
        $event->setStartDatetime($data["startDate"], $data["startTime"] ?? null);

        error_log("Definindo endDatetime: {$data['endDate']} {$data['endTime']}");
        $event->setEndDatetime($data["endDate"], $data["endTime"] ?? null);

        $event->setStatus('PENDING');

        if (!$event->getStartDatetime() || !$event->getEndDatetime()) {
            error_log("ERRO: Falha ao converter datas");
            error_log("StartDatetime: " . ($event->getStartDatetime() ? $event->getStartDatetime()->format('Y-m-d H:i:s') : 'NULL'));
            error_log("EndDatetime: " . ($event->getEndDatetime() ? $event->getEndDatetime()->format('Y-m-d H:i:s') : 'NULL'));
            $this->call(400, "bad_request", "Formato de data inválido", "error")->back();
            return;
        }

        if ($event->getStartDatetime() >= $event->getEndDatetime()) {
            error_log("ERRO: Data de início posterior ou igual à data de término");
            $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
            return;
        }

        error_log("Tentando inserir evento no banco...");

        if (!$event->insert()) {
            $errorMsg = $event->getErrorMessage() ?? "Erro desconhecido ao criar evento";
            error_log("ERRO ao inserir: " . $errorMsg);
            $this->call(500, "internal_server_error", $errorMsg, "error")->back();
            return;
        }

        error_log("Evento criado com sucesso! ID: " . $event->getId());

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
            "status" => $event->getStatus(),
            "attractions" => $event->getAttractions()
        ];

        error_log("===== CREATE EVENT - FIM (SUCESSO) =====");

        $this->call(201, "created", "Evento criado com sucesso e aguardando aprovação", "success")
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

        $currentStartDatetime = $event->getStartDatetime();
        $currentEndDatetime = $event->getEndDatetime();

        if (isset($data["startDate"]) && isset($data["startTime"])) {
            $event->setStartDatetime($data["startDate"], $data["startTime"]);
            if (!$event->getStartDatetime()) {
                $this->call(400, "bad_request", "Data/hora de início inválida", "error")->back();
                return;
            }
        }

        if (isset($data["endDate"]) && isset($data["endTime"])) {
            $event->setEndDatetime($data["endDate"], $data["endTime"]);
            if (!$event->getEndDatetime()) {
                $this->call(400, "bad_request", "Data/hora de término inválida", "error")->back();
                return;
            }
        }

        if ($event->getStartDatetime() >= $event->getEndDatetime()) {
            $event->setStartDatetime($currentStartDatetime);
            $event->setEndDatetime($currentEndDatetime);
            $this->call(400, "bad_request", "Data de início deve ser anterior à data de término", "error")->back();
            return;
        }

        if (!$event->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao atualizar evento: " . $event->getErrorMessage(), "error")->back();
            return;
        }

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
            "organizerId" => $event->getOrganizerId(),
            "status" => $event->getStatus()
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