<?php

namespace Source\Models\Records;

class Address
{
    protected $id;
    protected $idForeign;
    protected $zipCode;
    protected $street;
    protected $number;
    protected $complement;
    protected $deleted;


    public function __construct (
        int $id = null,
        int $idForeign = null,
        string $zipCode = null,
        string $street = null,
        string $number = null,
        string $complement = null,
        bool $deleted = false,

    )
    {
        $this->id = $id;
        $this->idForeign = $idForeign;
        $this->zipCode = $zipCode;
        $this->street = $street;
        $this->number = $number;
        $this->complement = $complement;
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

    public function getIdForeign(): ?int
    {
        return $this->idForeign;
    }

    public function setIdForeign(?int $idForeign): void
    {
        $this->idForeign = $idForeign;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): void
    {
        $this->complement = $complement;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted): void
    {
        $this->deleted = $deleted;
    }





}