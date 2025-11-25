<?php

class Sample
{
    private string $id;
    private string $type;
    private string $priority;
    private int $analysisTime;
    private string $arrivalTime;
    private string $patientId;

    public function __construct(
        string $id,
        string $type,
        string $priority,
        int $analysisTime,
        string $arrivalTime,
        string $patientId
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->priority = $priority;
        $this->analysisTime = $analysisTime;
        $this->arrivalTime = $arrivalTime;
        $this->patientId = $patientId;
    }


    public function getId(): string
    {
        return $this->id;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getPriority(): string
    {
        return $this->priority;
    }
    public function getAnalysisTime(): int
    {
        return $this->analysisTime;
    }
    public function getArrivalTime(): string
    {
        return $this->arrivalTime;
    }
    public function getPatientId(): string
    {
        return $this->patientId;
    }
}
