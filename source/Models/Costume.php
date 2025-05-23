<?php 

namespace Source\Models;

use Source\Core\Connect;
use Source\Core\Model;

class Costume extends Model{

    protected $id;
    protected $playId;
    protected $description;
    protected $deleted;



    public function __construct(int $id = null, string $description = null, int $playId = null,         bool $deleted = false,
    ){
        $this->table = "costumes";
        $this->id = $id;
        $this->playId = $playId;
        $this->description = $description;
        $this->deleted = $deleted;

    }

    function getId(): ?int{
        return $this->id;
    }

    function getDescription(): ?string{
        return $this->description;
    }

    function getPlayId(): ?int {
        return $this->playId;
    }

    function setId(?int $id): void{
        $this->id = $id;
    }

    function setDescription(?string $description): void{
        $this->$description = $description;
    }

    function setPlaysId(?int $id): void{
        $this->id = $id;
    }
    public function getDeleted(): ?bool{
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): void{
        $this->deleted = $deleted;
    }

}
?>