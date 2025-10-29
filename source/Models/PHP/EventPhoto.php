<?php

namespace Source\Models\PHP;

use PDO;
use Source\Core\Connect;
use Source\Core\Model;

class EventPhoto extends Model
{
    private ?int $id = null;
    private ?int $eventId = null;
    private ?string $photo = null;
    private ?bool $isMain = false;

    public function __construct(
        ?int $id = null,
        ?int $eventId = null,
        ?string $photo = null,
        ?bool $isMain = false
    ) {
        $this->table = "event_photos";
        $this->id = $id;
        $this->eventId = $eventId;
        $this->photo = $photo;
        $this->isMain = $isMain;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getEventId(): ?int {
        return $this->eventId;
    }

    public function getPhoto(): ?string {
        return $this->photo;
    }

    public function getIsMain(): ?bool {
        return $this->isMain;
    }

    public function setEventId(?int $eventId): void {
        $this->eventId = $eventId;
    }

    public function setPhoto(?string $photo): void {
        $this->photo = $photo;
    }

    public function setIsMain(?bool $isMain): void {
        $this->isMain = $isMain;
    }

    public function findByEventId(int $eventId): array {
        try {
            $stmt = Connect::getInstance()->prepare("
                SELECT * FROM {$this->table} 
                WHERE eventId = :eventId 
                ORDER BY isMain DESC, id ASC
            ");
            $stmt->bindValue(":eventId", $eventId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->errorMessage = "Erro ao buscar fotos: " . $e->getMessage();
            return [];
        }
    }

    public function findMainPhotoByEventId(int $eventId): ?string {
        try {
            $stmt = Connect::getInstance()->prepare("
                SELECT photo FROM {$this->table} 
                WHERE eventId = :eventId AND isMain = 1 
                LIMIT 1
            ");
            $stmt->bindValue(":eventId", $eventId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['photo'] : null;
        } catch (\PDOException $e) {
            $this->errorMessage = "Erro ao buscar foto principal: " . $e->getMessage();
            return null;
        }
    }

    public function setAsMain(): bool {
        try {
            Connect::getInstance()->beginTransaction();

            $stmt = Connect::getInstance()->prepare("
                UPDATE {$this->table} 
                SET isMain = 0 
                WHERE eventId = :eventId
            ");
            $stmt->bindValue(":eventId", $this->eventId, PDO::PARAM_INT);
            $stmt->execute();

            // Define esta foto como principal
            $stmt = Connect::getInstance()->prepare("
                UPDATE {$this->table} 
                SET isMain = 1 
                WHERE id = :id
            ");
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
            $stmt->execute();

            Connect::getInstance()->commit();
            return true;
        } catch (\PDOException $e) {
            Connect::getInstance()->rollBack();
            $this->errorMessage = "Erro ao definir foto principal: " . $e->getMessage();
            return false;
        }
    }

    public function deletePhoto(): bool {
        try {
            // Buscar caminho da foto antes de deletar
            if ($this->findById($this->id)) {
                $photoPath = __DIR__ . '/../../' . $this->photo;

                $stmt = Connect::getInstance()->prepare("
                    DELETE FROM {$this->table} WHERE id = :id
                ");
                $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
                $stmt->execute();

                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }

                return true;
            }
            return false;
        } catch (\PDOException $e) {
            $this->errorMessage = "Erro ao deletar foto: " . $e->getMessage();
            return false;
        }
    }

    public function countByEventId(int $eventId): int {
        try {
            $stmt = Connect::getInstance()->prepare("
                SELECT COUNT(*) as total FROM {$this->table} 
                WHERE eventId = :eventId
            ");
            $stmt->bindValue(":eventId", $eventId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['total'] : 0;
        } catch (\PDOException $e) {
            $this->errorMessage = "Erro ao contar fotos: " . $e->getMessage();
            return 0;
        }
    }
}