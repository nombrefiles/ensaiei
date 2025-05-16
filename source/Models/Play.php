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

    public function __construct(int $id = null, string $name = null, string $genre = null, string $script = null, array $costumes = null, int $directorId = null, array $actors = null){
        $this->table = "plays";
        $this->id = $id;
        $this->name = $name;
        $this->genre = $genre;
        $this->script = $script;
        $this->costumes = $costumes;
        $this->directorId = $directorId;
        $this->actors = $actors;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getGenre(){
        return $this->genre;
    }

    public function getScript(){
        return $this->script;
    }

    public function getCostumes(){
        return $this->costumes;
    }

    public function getDirectorId(): int{
        return $this->directorId;
    }

    public function getActors(){
        return $this->actors;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setGenre($genre){
        $this->genre = $genre;
    }

    public function setScript($script){
        $this->script = $script;
    }

    public function setCostumes($costumes){
        $this->costumes = $costumes;
    }

    public function setDirectorId($directorId){
        $this->directorId = $directorId;
    }

    public function setActors($actors){
        $this->actors = $actors;
    }

    public function insertWithActors(): bool
    {
        $conn = \Source\Core\Connect::getInstance();
        $conn->beginTransaction();

        try {
            // 1. Inserir a peça na tabela plays
            $stmt = $conn->prepare("
            INSERT INTO plays (name, genre, script, directorId)
            VALUES (?, ?, ?, ?)
        ");
            $stmt->execute([
                $this->name,
                $this->genre,
                $this->script,
                $this->directorId // ← Atenção: aqui você está passando o ID direto, não um objeto
            ]);

            $this->id = $conn->lastInsertId();

            // 2. Inserir os atores relacionados
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