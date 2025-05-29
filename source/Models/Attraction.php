<?php

namespace Source\Models;

use PDO;
use Source\Core\Connect;
use Source\Core\Model;
use Source\enums\Type;

class Attraction extends Model
{
    protected $id;
    protected $name;
    protected $type;
    protected $eventId;
    protected $startDatetime;
    protected $endDatetime;
    protected $specificLocation;
    protected $performers;
    protected $deleted;

    public function __construct( int $id = null, string $name = null, Type $type = Type::OTHER, $eventId = null, $startDatetime = null, $endDatetime = null, $specificLocation = null, $performers = null, $deleted = false)
    {
        $this->table = "attractions";
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->eventId = $eventId;
        $this->startDatetime = $startDatetime;
        $this->endDatetime = $endDatetime;
        $this->specificLocation = $specificLocation;
        $this->performers = $performers;
        $this->deleted = $deleted;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getEventId()
    {
        return $this->eventId;
    }

    public function setEventId($eventId): void
    {
        $this->eventId = $eventId;
    }

    public function getStartDatetime()
    {
        return $this->startDatetime;
    }

    public function setStartDatetime($startDatetime): void
    {
        $this->startDatetime = $startDatetime;
    }

    public function getEndDatetime()
    {
        return $this->endDatetime;
    }

    public function setEndDatetime($endDatetime): void
    {
        $this->endDatetime = $endDatetime;
    }

    public function getSpecificLocation()
    {
        return $this->specificLocation;
    }

    public function setSpecificLocation($specificLocation): void
    {
        $this->specificLocation = $specificLocation;
    }

    public function getPerformers()
    {
        return $this->performers;
    }

    public function setPerformers($performers): void
    {
        $this->performers = $performers;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted): void
    {
        $this->deleted = $deleted;
    }

    // metodos

    public function findById(int $id): bool
    {
        $stmt = Connect::getInstance()->prepare("SELECT * FROM attractions WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if ($attraction = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->fill($attraction);

            $stmtPerformers = Connect::getInstance()->prepare("SELECT actorId FROM attractions_performers WHERE attractionId = :attractionId");
            $stmtPerformers->bindParam(":attractionId", $id);
            $stmtPerformers->execute();
            $performers = $stmtPerformers->fetchAll(PDO::FETCH_COLUMN);

            $this->performers = $performers ?: [];

            return true;
        }

        return false;
    }

    public function insertWithPerformers(): bool
    {
        $conn = \Source\Core\Connect::getInstance();
        $conn->beginTransaction();

        try {

            $stmt = $conn->prepare("
            INSERT INTO attractions (name, type, eventId, startDatetime, endDatetime, specificLocation)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
            $stmt->execute([
                $this->name,
                $this->type,
                $this->eventId,
                $this->startDatetime,
                $this->endDatetime,
                $this->specificLocation
            ]);

            $this->id = $conn->lastInsertId();

            if (!empty($this->performers) && is_array($this->performers)) {
                $stmtPerformer = $conn->prepare("
                INSERT INTO attractions_performers (attractionId, performerId)
                VALUES (?, ?)
            ");

                foreach ($this->performers as $performerId) {
                    $stmtPerformer->execute([$performerId, $this->id]);
                }
            }

            $conn->commit();
            return true;
        } catch (\PDOException $e) {
            $conn->rollBack();
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    public function updateWithPerformers(): bool
    {
        $conn = \Source\Core\Connect::getInstance();
        try {
            $conn->beginTransaction();

            // Atualiza os dados principais da atração
            $stmt = $conn->prepare("
            UPDATE attractions 
            SET name = ?, 
                type = ?, 
                eventId = ?, 
                startDatetime = ?, 
                endDatetime = ?, 
                specificLocation = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $this->name,
            $this->type,
            $this->eventId,
            $this->startDatetime,
            $this->endDatetime,
            $this->specificLocation,
            $this->id
        ]);

        $stmtDelete = $conn->prepare("DELETE FROM attractions_performers WHERE attractionId = ?");
        $stmtDelete->execute([$this->id]);

            // Insere novos performers
        if (!empty($this->performers) && is_array($this->performers)) {
            $stmtInsert = $conn->prepare("INSERT INTO attractions_performers (attractionId, performerId) VALUES (?, ?)");
            
            foreach ($this->performers as $performerId) {
                $stmtInsert->execute([$this->id, $performerId]);
            }
        }

        $conn->commit();
        return true;

    } catch (\PDOException $e) {
        $conn->rollBack();
        $this->errorMessage = "Erro ao atualizar atração: " . $e->getMessage();
        return false;
    }
}

    private function fill(array $data)
    {
        $this->id = $data["id"] ?? null;
        $this->name = $data["name"] ?? null;
        $this->type = $data["type"] ?? Type::OTHER;
        $this->eventId = $data["eventId"] ?? null;
        $this->performers = $data["performers"] ?? null;
        $this->startDatetime = $data["startDatetime"] ?? null;
        $this->endDatetime = $data["endDatetime"] ?? null;
        $this->specificLocation = $data["specificLocation"] ?? null;
    }

}