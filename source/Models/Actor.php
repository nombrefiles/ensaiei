<?php 

namespace Source\Models;

use PDO;
use Source\Core\Connect;
use Source\Core\Model;

class Actor extends Model{

    protected $id;
    protected $name;
    protected $deleted;


    public function __construct(int $id = null, string $name = null, bool $deleted = false,
    ){
        $this->table = "actors";
        $this->id = $id;
        $this->name = $name;
        $this->deleted = $deleted;
    }

    function getId(): ?int {
        return $this->id;
    }

    function getName(): ?string{
        return $this->name;
    }

    function setId(?int $id): void{
        $this->id = $id;
    }

    function setName(?string $name): void{
        $this->name = $name;
    }

    public function getDeleted(): ?bool{
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): void{
        $this->deleted = $deleted;
    }

    public function findById(int $id): bool
    {
        $stmt = Connect::getInstance()->prepare("SELECT * FROM actors WHERE id = :id LIMIT 1");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if ($actor = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->fill($actor);
            return true;
        }

        return false;
    }

    private function fill(array $data)
    {
        $this->id = $data["id"] ?? null;
        $this->name = $data["name"] ?? null;
    }


}
   

?>