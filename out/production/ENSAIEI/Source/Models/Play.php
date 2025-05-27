<?php

namespace Source\Models;

use PDOException;
use Source\Core\Connect;
use Source\Core\Model;

class Play extends Model{
    protected $id;
    protected $name;
    protected $genre;
    protected $script;
    protected $costumes;
    protected $directorId;
    protected $actors;
    protected $deleted;


    public function __construct(int $id = null, string $name = null, string $genre = null, string $script = null, array $costumes = null, int $directorId = null, array $actors = null,         bool $deleted = false,
    ){
        $this->table = "plays";
        $this->id = $id;
        $this->name = $name;
        $this->genre = $genre;
        $this->script = $script;
        $this->costumes = $costumes;
        $this->directorId = $directorId;
        $this->actors = $actors;
        $this->deleted = $deleted;

    }

    public function getId(): ?int{
        return $this->id;
    }

    public function getName(): ?string{
        return $this->name;
    }

    public function getGenre(): ?string{
        return $this->genre;
    }

    public function getScript(): ?string{
        return $this->script;
    }

    public function getCostumes(): ?array{
        return $this->costumes;
    }

    public function getDirectorId(): ?int{
        return $this->directorId;
    }

    public function getActors(): ?array{
        return $this->actors;
    }

    public function setId(?int $id): void{
        $this->id = $id;
    }

    public function setName(?string $name): void{
        $this->name = $name;
    }

    public function setGenre(?string $genre): void{
        $this->genre = $genre;
    }

    public function setScript(?string $script): void{
        $this->script = $script;
    }

    public function setCostumes(?array$costumes): void{
        $this->costumes = $costumes;
    }

    public function setDirectorId(?int $directorId): void{
        $this->directorId = $directorId;
    }

    public function setActors(?array$actors): void{
        $this->actors = $actors;
    }

    public function getDeleted(): ?bool{
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): void{
        $this->deleted = $deleted;
    }

    public function insertWithActors(): bool
    {
        $conn = \Source\Core\Connect::getInstance();
        $conn->beginTransaction();

        try {
          
            $stmt = $conn->prepare("
            INSERT INTO plays (name, genre, script, directorId)
            VALUES (?, ?, ?, ?)
        ");
            $stmt->execute([
                $this->name,
                $this->genre,
                $this->script,
                $this->directorId 
            ]);

            $this->id = $conn->lastInsertId();

            if (!empty($this->actors) && is_array($this->actors)) {
                $stmtActor = $conn->prepare("
                INSERT INTO actors_plays (actorId, playId)
                VALUES (?, ?)
            ");

                foreach ($this->actors as $actorId) {
                    $stmtActor->execute([$actorId, $this->id]);
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

}

?>