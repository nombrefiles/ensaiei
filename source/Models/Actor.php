<?php 

namespace Source\Models;

use Source\Core\Connect;
use Source\Core\Model;

class Actor extends Model{

    protected $id;
    protected $name;
    protected $plays;

    public function __construct(int $id = null, string $name = null, array $plays = null){
        $this->table = "actors";
        $this->id = $id;
        $this->name = $name;
        $this->plays = $plays;
    }

    function getId(){
        return $this->id;
    }

    function getName(){
        return $this->name;
    }

    function getPlays(){
        return $this->plays;
    }

    function setId($id){
        $this->id = $id;
    }

    function setName($name){
        $this->name = $name;
    }

    function setPlays($plays){
        $this->plays = $plays;
    }

}
   

?>