<?php

namespace Source\Models;

use DateTime;
use PDO;
use PDOException;
use Source\Core\Connect;
use source\Core\Model;

class Event extends Model {
    private $id;
    private $title;
    private $description;
    private $location;
    private $latitude;
    private $longitude;
    private $startDatetime;
    private $endDatetime;
    private $deleted;
    private $organizerId;
    private array $attractions = [];

    public function __construct(
        int $id = null,
        string $title = null,
        string $description = null,
        string $location = null,
        float $latitude = null,
        float $longitude = null,
        DateTime $startDatetime = null,
        DateTime $endDatetime = null,
        bool $deleted = null,
        int $organizerId = null
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

    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
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

    public function getStartDatetime(): DateTime {
        return $this->startDatetime;
    }

    public function getEndDatetime(): DateTime {
        return $this->endDatetime;
    }

    public function isDeleted(): bool {
        return $this->deleted;
    }

    public function getOrganizerId(): int {
        return $this->organizerId;
    }

    /**
     * @return array
     */
    public function getAttractions(): array {
        if (empty($this->attractions)) {
            $this->loadAttractions();
        }
        return $this->attractions;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setTitle(string $title): void {
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

    public function setStartDatetime(DateTime $startDatetime): void {
        $this->startDatetime = $startDatetime;
    }

    public function setEndDatetime(DateTime $endDatetime): void {
        $this->endDatetime = $endDatetime;
    }

    public function setDeleted(bool $deleted): void {
        $this->deleted = $deleted;
    }

    public function setOrganizerId(int $organizerId): void {
        $this->organizerId = $organizerId;
    }

    /**
     * @param array $attractions
     */
    public function setAttractions(array $attractions): void {
        $this->attractions = $attractions;
    }

    /**
     * Carrega as atrações do evento do banco de dados
     */
    private function loadAttractions(): void {
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

    /**
     * Adiciona uma atração ao evento
     * @param array $attraction
     */
    public function addAttraction(array $attraction): void {
        $this->attractions[] = $attraction;
    }

    /**
     * Remove uma atração do evento pelo ID
     * @param int $attractionId
     */
    public function removeAttraction(int $attractionId): void {
        $this->attractions = array_filter($this->attractions, function($attraction) use ($attractionId) {
            return $attraction['id'] !== $attractionId;
        });
    }

    /**
     * Verifica se o evento tem atrações
     * @return bool
     */
    public function hasAttractions(): bool {
        return !empty($this->getAttractions());
    }

    /**
     * Conta o número de atrações do evento
     * @return int
     */
    public function countAttractions(): int {
        return count($this->getAttractions());
    }
}