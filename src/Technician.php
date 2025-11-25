<?php

class Technician
{

    use ReservableTrait;

    private string $id;
    private string $name;
    private string $speciality;
    private string $startTime;
    private string $endTime;

    public function __construct(
        string $id,
        string $name,
        string $speciality,
        string $startTime,
        string $endTime
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->speciality = $speciality;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getSpeciality(): string
    {
        return $this->speciality;
    }
    public function getStartTime(): string
    {
        return $this->startTime;
    }
    public function getEndTime(): string
    {
        return $this->endTime;
    }
}
