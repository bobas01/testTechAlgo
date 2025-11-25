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

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    public function isCompatibleWith(string $sampleType): bool
    {
        return $this->type === $sampleType;
    }

    public function isAvailableAt(int $startMinutes, int $durationMinutes): bool
    {
        if (!$this->available) {
            return false;
        }

        $endMinutes = $startMinutes + $durationMinutes;

        foreach ($this->getReservations() as $reservation) {
            $resStart = $this->timeToMinutes($reservation['start']);
            $resEnd = $this->timeToMinutes($reservation['end']);

            if (!($endMinutes <= $resStart || $startMinutes >= $resEnd)) {
                return false;
            }
        }

        return true;
    }
}
