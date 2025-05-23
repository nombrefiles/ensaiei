<?php 

namespace Source\Models;

use Source\Core\Connect;
use Source\Core\Model;

class Actor extends Model{

    protected $id;
    protected $name;
    protected $plays;
    protected $deleted;


    public function __construct(int $id = null, string $name = null, array $plays = null,         bool $deleted = false,
    ){
        $this->table = "actors";
        $this->id = $id;
        $this->name = $name;
        $this->plays = $plays;
        $this->deleted = $deleted;
    }

    function getId(): ?int {
        return $this->id;
    }

    function getName(): ?string{
        return $this->name;
    }

    function getPlays(): ?array{
        return $this->plays;
    }

    function setId(?int $id): void{
        $this->id = $id;
    }

    function setName(?string $name): void{
        $this->name = $name;
    }

    function setPlays(?array $plays): void{
        $this->plays = $plays;
    }
    public function getDeleted(): ?bool{
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): void{
        $this->deleted = $deleted;
    }




}
   

?>