<?php

namespace Source\Models\PHP;

use DateTime;
use PDO;
use PDOException;
use Source\Core\Connect;
use Source\Core\Model;
use Source\Utils\DateBr;

class Event extends Model {
    private ?int $id = null;
    private ?string $title = null;
    private ?string $description = null;
    private ?string $location = null;
    private ?float $latitude = null;
    private ?float $longitude = null;
    private ?DateTime $startDatetime = null;
    private ?DateTime $endDatetime = null;
    private ?bool $deleted = null;
    private ?int $organizerId = null;
    private array $attractions = [];

    public function __construct(
        ?int $id = null,
        ?string $title = null,
        ?string $description = null,
        ?string $location = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?DateTime $startDatetime = null,
        ?DateTime $endDatetime = null,
        ?bool $deleted = false,
        ?int $organizerId = null
    ) {
        $this->table = "events";
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->location = $location;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->startDatetime = $startDatetime;
        $this->endDatetime = $endDatetime;
        $this->deleted = $deleted;
        $this->organizerId = $organizerId;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): ?string {
        return $this->title;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function getLatitude(): ?float {
        return $this->latitude;
    }

    public function getLongitude(): ?float {
        return $this->longitude;
    }

    public function getStartDatetime(): ?DateTime {
        return $this->startDatetime;
    }

    public function getEndDatetime(): ?DateTime {
        return $this->endDatetime;
    }

    public function isDeleted(): ?bool {
        return $this->deleted;
    }

    public function getOrganizerId(): ?int {
        return $this->organizerId;
    }

    public function getAttractions(): array {
        if (empty($this->attractions) && $this->id) {
            $this->loadAttractions();
        }
        return $this->attractions;
    }

    public function getStartDate(): string {
        return $this->startDatetime ? $this->startDatetime->format('d/m/Y') : '';
    }

    public function getStartTime(): string {
        return $this->startDatetime ? $this->startDatetime->format('H:i:s') : '';
    }

    public function getEndDate(): string {
        return $this->endDatetime ? $this->endDatetime->format('d/m/Y') : '';
    }

    public function getEndTime(): string {
        return $this->endDatetime ? $this->endDatetime->format('H:i:s') : '';
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setTitle(?string $title): void {
        $this->title = $title;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function setLocation(?string $location): void {
        $this->location = $location;
    }

    public function setLatitude(?float $latitude): void {
        $this->latitude = $latitude;
    }

    public function setLongitude(?float $longitude): void {
        $this->longitude = $longitude;
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

    public function setDeleted(?bool $deleted): void {
        $this->deleted = $deleted;
    }

    public function setOrganizerId(?int $organizerId): void {
        $this->organizerId = $organizerId;
    }

    public function setAttractions(array $attractions): void {
        $this->attractions = $attractions;
    }

    private function loadAttractions(): void {
        if (!$this->id) {
            $this->attractions = [];
            return;
        }
        try {
            $stmt = Connect::getInstance()->prepare("
                SELECT * FROM attractions 
                WHERE eventId = :eventId 
                AND deleted = false
            ");
            $stmt->bindValue(":eventId", $this->id, PDO::PARAM_INT);
            $stmt->execute();
            $this->attractions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->errorMessage = "Erro ao carregar atrações do evento: " . $e->getMessage();
            $this->attractions = [];
        }
    }

    public function addAttraction(array $attraction): void {
        if (isset($attraction['id'], $attraction['name'])) {
            $this->attractions[] = $attraction;
        }
    }

    public function removeAttraction(int $attractionId): void {
        $this->attractions = array_filter($this->attractions, function($attraction) use ($attractionId) {
            return $attraction['id'] !== $attractionId;
        });
    }

    public function hasAttractions(): bool {
        return !empty($this->getAttractions());
    }

    public function countAttractions(): int {
        return count($this->getAttractions());
    }
public function updateById(): bool
{
    try {
        $stmt = Connect::getInstance()->prepare("
            UPDATE {$this->table} 
            SET title = :title,
                description = :description,
                location = :location,
                startDatetime = :startDatetime,
                endDatetime = :endDatetime
            WHERE id = :id
        ");

        $stmt->bindValue(":title", $this->title, \PDO::PARAM_STR);
        $stmt->bindValue(":description", $this->description, \PDO::PARAM_STR);
        $stmt->bindValue(":location", $this->location, \PDO::PARAM_STR);
        $stmt->bindValue(":startDatetime", $this->startDatetime ? $this->startDatetime->format('Y-m-d H:i:s') : null, \PDO::PARAM_STR);
        $stmt->bindValue(":endDatetime", $this->endDatetime ? $this->endDatetime->format('Y-m-d H:i:s') : null, \PDO::PARAM_STR);
        $stmt->bindValue(":id", $this->id, \PDO::PARAM_INT);

        return $stmt->execute();
    } catch (\PDOException $e) {
        $this->errorMessage = $e->getMessage();
        return false;
    }
}
}