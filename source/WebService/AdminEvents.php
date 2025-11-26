<?php

namespace Source\WebService;

use Source\Models\Event;
use Source\Models\User;
use Source\Enums\Role;
use PDO;

class AdminEvents extends Api
{
    private function checkAdmin(): bool
    {
        $this->auth();

        $user = new User();
        if (!$user->findById($this->userAuth->id)) {
            $this->call(404, "not_found", "Usuário não encontrado", "error")->back();
            return false;
        }

        if ($user->getRole() !== Role::ADMIN) {
            $this->call(403, "forbidden", "Acesso negado. Apenas administradores podem realizar esta ação.", "error")->back();
            return false;
        }

        return true;
    }

    public function listPendingEvents(): void
    {
        if (!$this->checkAdmin()) {
            return;
        }

        try {
            $stmt = \Source\Core\Connect::getInstance()->prepare("
                SELECT e.*, u.name as organizerName, u.username as organizerUsername
                FROM events e
                LEFT JOIN users u ON e.organizerId = u.id
                WHERE e.status = 'PENDING' AND e.deleted = false
                ORDER BY e.startDatetime ASC
            ");

            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->call(200, "success", "Eventos pendentes de aprovação", "success")->back($events);
        } catch (\PDOException $e) {
            $this->call(500, "internal_server_error", "Erro ao buscar eventos: " . $e->getMessage(), "error")->back();
        }
    }

    public function approveEvent(array $data): void
    {
        if (!$this->checkAdmin()) {
            return;
        }

        if (!isset($data["id"])) {
            $this->call(400, "bad_request", "ID do evento não fornecido", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($data["id"])) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        if ($event->getStatus() === 'APPROVED') {
            $this->call(400, "bad_request", "Evento já foi aprovado", "error")->back();
            return;
        }

        $event->setStatus('APPROVED');
        $event->setReviewedBy($this->userAuth->id);

        if (!$event->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao aprovar evento: " . $event->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $event->getId(),
            "title" => $event->getTitle(),
            "status" => $event->getStatus(),
            "reviewedBy" => $event->getReviewedBy()
        ];

        $this->call(200, "success", "Evento aprovado com sucesso", "success")->back($response);
    }

    public function rejectEvent(array $data): void
    {
        if (!$this->checkAdmin()) {
            return;
        }

        if (!isset($data["id"])) {
            $this->call(400, "bad_request", "ID do evento não fornecido", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($data["id"])) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        if ($event->getStatus() === 'REJECTED') {
            $this->call(400, "bad_request", "Evento já foi rejeitado", "error")->back();
            return;
        }

        $event->setStatus('REJECTED');
        $event->setReviewedBy($this->userAuth->id);

        if (!$event->updateById()) {
            $this->call(500, "internal_server_error", "Erro ao rejeitar evento: " . $event->getErrorMessage(), "error")->back();
            return;
        }

        $response = [
            "id" => $event->getId(),
            "title" => $event->getTitle(),
            "status" => $event->getStatus(),
            "reviewedBy" => $event->getReviewedBy()
        ];

        $this->call(200, "success", "Evento rejeitado", "success")->back($response);
    }

    public function getEventStats(): void
    {
        if (!$this->checkAdmin()) {
            return;
        }

        try {
            $stmt = \Source\Core\Connect::getInstance()->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'PENDING' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'APPROVED' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'REJECTED' THEN 1 ELSE 0 END) as rejected
                FROM events
                WHERE deleted = false
            ");

            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->call(200, "success", "Estatísticas dos eventos", "success")->back($stats);
        } catch (\PDOException $e) {
            $this->call(500, "internal_server_error", "Erro ao buscar estatísticas: " . $e->getMessage(), "error")->back();
        }
    }
}