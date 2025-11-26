<?php

class Scheduler
{
    private array $samples = [];
    private array $technicians = [];
    private array $equipment = [];
    private array $schedule = [];

    public function __construct(array $data)
    {
       
        foreach ($data['samples'] ?? [] as $sampleData) {
            $this->samples[] = new Sample(
                $sampleData['id'],
                $sampleData['type'],
                $sampleData['priority'],
                $sampleData['analysisTime'],
                $sampleData['arrivalTime'],
                $sampleData['patientId']
            );
        }

        foreach ($data['technicians'] ?? [] as $techData) {
            $this->technicians[] = new Technician(
                $techData['id'],
                $techData['name'] ?? $techData['id'],
                $techData['speciality'],
                $techData['startTime'],
                $techData['endTime']
            );
        }

        foreach ($data['equipment'] ?? [] as $equipData) {
            $this->equipment[] = new Equipment(
                $equipData['id'],
                $equipData['name'] ?? $equipData['id'],
                $equipData['type'],
                $equipData['available'] ?? true
            );
        }
    }

    public function planifyLab(): array
    {
        $sortedSamples = $this->sortSamplesByPriority();

        foreach ($sortedSamples as $sample) {
            $this->scheduleSample($sample);
        }

        $metrics = $this->calculateMetrics();

        return [
            'schedule' => $this->schedule,
            'metrics' => $metrics
        ];
    }

    
    private function sortSamplesByPriority(): array
    {
        $samples = $this->samples;

        usort($samples, function ($a, $b) {
            $priorityOrder = ['STAT' => 3, 'URGENT' => 2, 'ROUTINE' => 1];

            $priorityA = $priorityOrder[$a->getPriority()] ?? 0;
            $priorityB = $priorityOrder[$b->getPriority()] ?? 0;

            if ($priorityA !== $priorityB) {
                return $priorityB - $priorityA;
            }

            return $a->getArrivalTimeInMinutes() - $b->getArrivalTimeInMinutes();
        });

        return $samples;
    }

    private function scheduleSample(Sample $sample): void
    {
        $technician = $this->findCompatibleTechnician($sample);
        if (!$technician) {
            return;
        }

        $equip = $this->findCompatibleEquipment($sample);
        if (!$equip) {
            return;
        }

        $slot = $this->getNextAvailableSlot($sample, $technician, $equip);
        if (!$slot) {
            return;
        }

        $startTime = $this->minutesToTime($slot['start']);
        $endTime = $this->minutesToTime($slot['end']);

        $technician->addReservation($startTime, $endTime, $sample->getId());
        $equip->addReservation($startTime, $endTime, $sample->getId());

        $this->schedule[] = [
            'sampleId' => $sample->getId(),
            'patientId' => $sample->getPatientId(),
            'type' => $sample->getType(),
            'priority' => $sample->getPriority(),
            'technicianId' => $technician->getId(),
            'technicianName' => $technician->getName(),
            'equipmentId' => $equip->getId(),
            'equipmentName' => $equip->getName(),
            'startTime' => $startTime,
            'endTime' => $endTime,
            'duration' => $sample->getAnalysisTime()
        ];
    }

    private function findCompatibleTechnician(Sample $sample): ?Technician
    {
        foreach ($this->technicians as $tech) {
            if ($tech->isCompatibleWith($sample->getType())) {
                return $tech;
            }
        }
        return null;
    }

    private function findCompatibleEquipment(Sample $sample): ?Equipment
    {
        foreach ($this->equipment as $equip) {
            if ($equip->isCompatibleWith($sample->getType()) && $equip->isAvailable()) {
                return $equip;
            }
        }
        return null;
    }

    private function getNextAvailableSlot(Sample $sample, Technician $tech, Equipment $equip): ?array
    {
        $duration = $sample->getAnalysisTime();
        $arrivalMinutes = $sample->getArrivalTimeInMinutes();

        $startMinutes = max($arrivalMinutes, $tech->getStartTimeInMinutes());
        $endMinutes = $tech->getEndTimeInMinutes();

        for ($currentStart = $startMinutes; $currentStart + $duration <= $endMinutes; $currentStart += 15) {
            $currentEnd = $currentStart + $duration;

            if (!$tech->isAvailable($currentStart, $duration)) {
                continue;
            }

            if (!$equip->isAvailableAt($currentStart, $duration)) {
                continue;
            }

            return [
                'start' => $currentStart,
                'end' => $currentEnd
            ];
        }

            return null;
    }

    private function calculateMetrics(): array
    {
        if (empty($this->schedule)) {
            return [
                'totalTime' => 0,
                'efficiency' => 0,
                'conflicts' => 0,
                'scheduledCount' => 0,
                'totalSamples' => count($this->samples)
            ];
        }

        $sortedSchedule = $this->schedule;
        usort($sortedSchedule, function ($a, $b) {
            return $this->timeToMinutes($a['startTime']) - $this->timeToMinutes($b['startTime']);
        });

        $firstStart = $this->timeToMinutes($sortedSchedule[0]['startTime']);
        $lastEnd = $this->timeToMinutes($sortedSchedule[count($sortedSchedule) - 1]['endTime']);
        $totalTime = $lastEnd - $firstStart;

        $conflicts = $this->countConflicts();

        $totalWorkTime = array_sum(array_column($this->schedule, 'duration'));
        $efficiency = $totalTime > 0 ? ($totalWorkTime / $totalTime) * 100 : 0;

        return [
            'totalTime' => $totalTime,
            'efficiency' => round($efficiency, 2),
            'conflicts' => $conflicts,
            'scheduledCount' => count($this->schedule),
            'totalSamples' => count($this->samples)
        ];
    }

    private function countConflicts(): int
    {
        $conflicts = 0;

        foreach ($this->schedule as $i => $item1) {
            foreach ($this->schedule as $j => $item2) {
                if ($i >= $j) continue;

                if (
                    $item1['technicianId'] === $item2['technicianId'] ||
                    $item1['equipmentId'] === $item2['equipmentId']
                ) {

                    if ($this->hasOverlap($item1, $item2)) {
                        $conflicts++;
                    }
                }
            }
        }

        return $conflicts;
    }

    private function hasOverlap(array $item1, array $item2): bool
    {
        $start1 = $this->timeToMinutes($item1['startTime']);
        $end1 = $this->timeToMinutes($item1['endTime']);
        $start2 = $this->timeToMinutes($item2['startTime']);
        $end2 = $this->timeToMinutes($item2['endTime']);

        return !($end1 <= $start2 || $start1 >= $end2);
    }

    private function timeToMinutes(string $time): int
    {
        [$hours, $minutes] = explode(':', $time);
        return (int)$hours * 60 + (int)$minutes;
    }

    private function minutesToTime(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }
}
