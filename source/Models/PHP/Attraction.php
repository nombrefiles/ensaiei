<?php

namespace Source\Models\PHP;

use DateTime;
use PDO;
use Source\Core\Connect;
use Source\Core\Model;
use Source\Enums\Type;
use Source\Utils\DateBr;

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

    public function __construct( int $id = null, string $name = null, Type $type = Type::OTHER, int $eventId = null, DateTime $startDatetime = null, DateTime $endDatetime = null, string $specificLocation = null, array $performers = null, bool $deleted = false)
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

    public function setStartDatetime($date, ?string $time = null): void {
        if ($date instanceof DateTime) {
            $this->startDatetime = $date;
            return;
        }
        $datetime = DateBr::convertToDateTime($date, $time);
        if ($datetime) {
            $this->startDatetime = $datetime;
        }
    }

    public function getStartDate(): string {
        if (!$this->startDatetime) {
            return '';
        }
        
        if (is_string($this->startDatetime)) {
            $this->startDatetime = new DateTime($this->startDatetime);
        }
        
        return $this->startDatetime->format('d/m/Y');
    }

    public function getStartTime(): string {
        if (!$this->startDatetime) {
            return '';
        }
        
        if (is_string($this->startDatetime)) {
            $this->startDatetime = new DateTime($this->startDatetime);
        }
        
        return $this->startDatetime->format('H:i');
    }

    public function getEndDatetime()
    {
        return $this->endDatetime;
    }

    public function setEndDatetime($date, ?string $time = null): void {
        if ($date instanceof DateTime) {
            $this->endDatetime = $date;
            return;
        }
        $datetime = DateBr::convertToDateTime($date, $time);
        if ($datetime) {
            $this->endDatetime = $datetime;
        }
    }

    public function getEndDate(): string {
        if (!$this->endDatetime) {
            return '';
        }
        
        if (is_string($this->endDatetime)) {
            $this->endDatetime = new DateTime($this->endDatetime);
        }
        
        return $this->endDatetime->format('d/m/Y');
    }

    public function getEndTime(): string {
        if (!$this->endDatetime) {
            return '';
        }
        
        if (is_string($this->endDatetime)) {
            $this->endDatetime = new DateTime($this->endDatetime);
        }
        
        return $this->endDatetime->format('H:i');
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

    public function findById(int $id): bool
    {
        $stmt = Connect::getInstance()->prepare("SELECT * FROM attractions WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if ($attraction = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->fill($attraction);

            $stmtPerformers = Connect::getInstance()->prepare("SELECT userId FROM attractions_performers WHERE attractionId = :attractionId");
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
            $this->type->value,
            $this->eventId,
            $this->startDatetime->format('Y-m-d H:i:s'),
            $this->endDatetime->format('Y-m-d H:i:s'),
            $this->specificLocation
        ]);

        $this->id = $conn->lastInsertId();

        if (!empty($this->performers) && is_array($this->performers)) {
            $stmtPerformer = $conn->prepare("
                INSERT INTO attractions_performers (attractionId, userId)
                VALUES (?, ?)
            ");

            foreach ($this->performers as $performerId) {
                $stmtPerformer->execute([$this->id, $performerId]);
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

        error_log("Iniciando atualização com performers: " . print_r($this->performers, true));

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
            $this->type->value,
            $this->eventId,
            $this->startDatetime instanceof DateTime ? $this->startDatetime->format('Y-m-d H:i:s') : $this->startDatetime,
            $this->endDatetime instanceof DateTime ? $this->endDatetime->format('Y-m-d H:i:s') : $this->endDatetime,
            $this->specificLocation,
            $this->id
        ]);

        error_log("Deletando performers antigos para attractionId: " . $this->id);
        $stmtDelete = $conn->prepare("DELETE FROM attractions_performers WHERE attractionId = ?");
        $stmtDelete->execute([$this->id]);

        if (!empty($this->performers) && is_array($this->performers)) {
            error_log("Inserindo novos performers: " . print_r($this->performers, true));
            $stmtInsert = $conn->prepare("INSERT INTO attractions_performers (attractionId, userId) VALUES (?, ?)");
            
            foreach ($this->performers as $performerId) {
                error_log("Inserindo performer ID: " . $performerId);
                $stmtInsert->execute([$this->id, $performerId]);
            }
        }

        $conn->commit();
        error_log("Commit realizado com sucesso");
        return true;

    } catch (\PDOException $e) {
        $conn->rollBack();
        error_log("Erro na atualização: " . $e->getMessage());
        $this->errorMessage = "Erro ao atualizar atração: " . $e->getMessage();
        return false;
    }
}

private function fill(array $data)
{
    $this->id = $data["id"] ?? null;
    $this->name = $data["name"] ?? null;
    
    // Tratamento especial para o type
    if (isset($data["type"]) && !empty($data["type"])) {
        try {
            $this->type = Type::from($data["type"]);
        } catch (\ValueError $e) {
            $this->type = Type::OTHER;
        }
    } else {
        $this->type = Type::OTHER;
    }
    
    $this->eventId = $data["eventId"] ?? null;
    $this->startDatetime = isset($data["startDatetime"]) && !empty($data["startDatetime"]) 
        ? new DateTime($data["startDatetime"]) 
        : null;
    $this->endDatetime = isset($data["endDatetime"]) && !empty($data["endDatetime"]) 
        ? new DateTime($data["endDatetime"]) 
        : null;
    $this->specificLocation = $data["specificLocation"] ?? null;
    $this->performers = $data["performers"] ?? null;
}

    public function findByEventId(int $eventId): array
    {
        $stmt = Connect::getInstance()->prepare("
        SELECT * FROM attractions 
        WHERE eventId = :eventId AND deleted = false
    ");
        $stmt->bindParam(":eventId", $eventId);
        $stmt->execute();

        $attractions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $attraction = new Attraction();
            $attraction->findById($row["id"]);

            $attractions[] = $attraction;
        }

        return $attractions;
    }
}