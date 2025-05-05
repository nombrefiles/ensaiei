<?php 

namespace Source\Models;

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

    public function __construct(int $id = null, string $name = null, string $genre = null, string $script = null, array $costumes = null, int $directorId = null, array $actors){
        $this->table = "actors";
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

    public function getDirectorId(){
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
}

?>