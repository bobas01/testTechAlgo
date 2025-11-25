<?php

trait ReservableTrait
{
    private array $reservations = [];

    public function getReservations(): array
    {
        return $this->reservations;
    }

    public function addReservation(string $startTime, string $endTime, string $sampleId): void
    {
        $this->reservations[] = [
            'start' => $startTime,
            'end' => $endTime,
            'sampleId' => $sampleId
        ];
    }

    public function removeReservation(string $sampleId): bool
    {
        foreach ($this->reservations as $key => $reservation) {
            if ($reservation['sampleId'] === $sampleId) {
                unset($this->reservations[$key]);
                $this->reservations = array_values($this->reservations);
                return true;
            }
        }
        return false;
    }

    public function findReservation(string $sampleId): ?array
    {
        foreach ($this->reservations as $reservation) {
            if ($reservation['sampleId'] === $sampleId) {
                return $reservation;
            }
        }
        return null;
    }

    public function clearReservations(): void
    {
        $this->reservations = [];
    }

    protected function timeToMinutes(string $time): int
    {
        [$hours, $minutes] = explode(':', $time);
        return (int)$hours * 60 + (int)$minutes;
    }
}
