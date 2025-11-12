<?php

namespace Source\Models;

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
    private ?string $status = 'PENDING';
    private ?string $rejectionReason = null;
    private ?DateTime $reviewedAt = null;
    private ?int $reviewedBy = null;
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
        ?int $organizerId = null,
        ?string $status = 'PENDING',
        ?string $rejectionReason = null,
        ?DateTime $reviewedAt = null,
        ?int $reviewedBy = null
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
        $this->status = $status;
        $this->rejectionReason = $rejectionReason;
        $this->reviewedAt = $reviewedAt;
        $this->reviewedBy = $reviewedBy;
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function getDescription(): ?string { return $this->description; }
    public function getLocation(): ?string { return $this->location; }
    public function getLatitude(): ?float { return $this->latitude; }
    public function getLongitude(): ?float { return $this->longitude; }
    public function getStartDatetime(): ?DateTime { return $this->startDatetime; }
    public function getEndDatetime(): ?DateTime { return $this->endDatetime; }
    public function isDeleted(): ?bool { return $this->deleted; }
    public function getOrganizerId(): ?int { return $this->organizerId; }
    public function getStatus(): ?string { return $this->status; }
    public function getRejectionReason(): ?string { return $this->rejectionReason; }
    public function getReviewedAt(): ?DateTime { return $this->reviewedAt; }
    public function getReviewedBy(): ?int { return $this->reviewedBy; }

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

    public function setId(?int $id): void { $this->id = $id; }
    public function setTitle(?string $title): void { $this->title = $title; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setLocation(?string $location): void { $this->location = $location; }
    public function setLatitude(?float $latitude): void { $this->latitude = $latitude; }
    public function setLongitude(?float $longitude): void { $this->longitude = $longitude; }
    public function setDeleted(?bool $deleted): void { $this->deleted = $deleted; }
    public function setOrganizerId(?int $organizerId): void { $this->organizerId = $organizerId; }
    public function setStatus(?string $status): void { $this->status = $status; }
    public function setRejectionReason(?string $reason): void { $this->rejectionReason = $reason; }
    public function setReviewedBy(?int $reviewedBy): void { $this->reviewedBy = $reviewedBy; }

    public function setReviewedAt($reviewedAt): void {
        if ($reviewedAt instanceof DateTime) {
            $this->reviewedAt = $reviewedAt;
        } elseif (is_string($reviewedAt)) {
            $this->reviewedAt = new DateTime($reviewedAt);
        } else {
            $this->reviewedAt = $reviewedAt;
        }
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
                    endDatetime = :endDatetime,
                    status = :status,
                    rejection_reason = :rejection_reason,
                    reviewed_at = :reviewed_at,
                    reviewed_by = :reviewed_by
                WHERE id = :id
            ");

            $stmt->bindValue(":title", $this->title, PDO::PARAM_STR);
            $stmt->bindValue(":description", $this->description, PDO::PARAM_STR);
            $stmt->bindValue(":location", $this->location, PDO::PARAM_STR);
            $stmt->bindValue(":startDatetime", $this->startDatetime ? $this->startDatetime->format('Y-m-d H:i:s') : null, PDO::PARAM_STR);
            $stmt->bindValue(":endDatetime", $this->endDatetime ? $this->endDatetime->format('Y-m-d H:i:s') : null, PDO::PARAM_STR);
            $stmt->bindValue(":status", $this->status, PDO::PARAM_STR);
            $stmt->bindValue(":rejection_reason", $this->rejectionReason, PDO::PARAM_STR);
            $stmt->bindValue(":reviewed_at", $this->reviewedAt ? $this->reviewedAt->format('Y-m-d H:i:s') : null, PDO::PARAM_STR);
            $stmt->bindValue(":reviewed_by", $this->reviewedBy, PDO::PARAM_INT);
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    public function findById(int $id): bool
    {
        try {
            $stmt = Connect::getInstance()->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return false;
            }

            $this->id = $result['id'];
            $this->title = $result['title'];
            $this->description = $result['description'];
            $this->location = $result['location'];
            $this->latitude = $result['latitude'];
            $this->longitude = $result['longitude'];
            $this->startDatetime = $result['startDatetime'] ? new DateTime($result['startDatetime']) : null;
            $this->endDatetime = $result['endDatetime'] ? new DateTime($result['endDatetime']) : null;
            $this->deleted = (bool)$result['deleted'];
            $this->organizerId = $result['organizerId'];
            $this->status = $result['status'] ?? 'PENDING';
            $this->rejectionReason = $result['rejection_reason'] ?? null;
            $this->reviewedAt = !empty($result['reviewed_at']) ? new DateTime($result['reviewed_at']) : null;
            $this->reviewedBy = $result['reviewed_by'] ?? null;

            return true;
        } catch (PDOException $e) {
            $this->errorMessage = "Erro ao buscar evento: " . $e->getMessage();
            return false;
        }
    }
}