<?php

class Equipment
{
    use ReservableTrait;

    private string $id;
    private string $name;
    private string $type;
    private bool $available;

    public function __construct(
        string $id,
        string $name,
        string $type,
        bool $available = true
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->available = $available;
    }

    // Getters
    public function getId(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function isAvailable(): bool
    {
        return $this->available;
    }

    // Setter
    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }
}
