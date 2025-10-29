<?php

namespace Source\Models\PHP;

use Source\Core\Model;

class Question extends Model
{
    private $id;
    private $idType;
    private $question;
    private $answer;
    protected $deleted;


    public function __construct (
        int $id = null,
        int $idType = null,
        string $question = null,
        string $answer = null,
        bool $deleted = false,

    )
    {
        $this->table = "questions";
        $this->id = $id;
        $this->idType = $idType;
        $this->question = $question;
        $this->answer = $answer;
        $this->deleted = $deleted;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getIdType(): ?int
    {
        return $this->idType;
    }

    public function setIdType(?int $idType): void
    {
        $this->idType = $idType;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(?string $question): void
    {
        $this->question = $question;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): void
    {
        $this->answer = $answer;
    }
    public function getDeleted(): ?bool{
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): void{
        $this->deleted = $deleted;
    }

}