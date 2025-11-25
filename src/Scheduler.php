<?php

class Scheduler
{
    private array $samples = [];
    private array $technicians = [];
    private array $equipment = [];
    private array $schedule = [];

    public function __construct(array $data)
    {
        // Créer les échantillons
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

        // Créer les techniciens
        foreach ($data['technicians'] ?? [] as $techData) {
            $this->technicians[] = new Technician(
                $techData['id'],
                $techData['name'],
                $techData['speciality'],
                $techData['startTime'],
                $techData['endTime']
            );
        }

        // Créer les équipements
        foreach ($data['equipment'] ?? [] as $equipData) {
            $this->equipment[] = new Equipment(
                $equipData['id'],
                $equipData['name'],
                $equipData['type'],
                $equipData['available'] ?? true
            );
        }
    }

    /**
     * Méthode principale de planification
     */
    public function planifyLab(): array
    {
        // 1. Trier les échantillons par priorité
        $sortedSamples = $this->sortSamplesByPriority();

        // 2. Planifier chaque échantillon
        foreach ($sortedSamples as $sample) {
            $this->scheduleSample($sample);
        }

        // 3. Calculer les métriques
        $metrics = $this->calculateMetrics();

        return [
            'schedule' => $this->schedule,
            'metrics' => $metrics
        ];
    }

    /**
     * Trie les échantillons par priorité : STAT > URGENT > ROUTINE
     * En cas d'égalité, tri par heure d'arrivée
     */
    private function sortSamplesByPriority(): array
    {
        $samples = $this->samples;

        usort($samples, function ($a, $b) {
            // Priorités : STAT = 3, URGENT = 2, ROUTINE = 1
            $priorityOrder = ['STAT' => 3, 'URGENT' => 2, 'ROUTINE' => 1];

            $priorityA = $priorityOrder[$a->getPriority()] ?? 0;
            $priorityB = $priorityOrder[$b->getPriority()] ?? 0;

            // Comparer par priorité
            if ($priorityA !== $priorityB) {
                return $priorityB - $priorityA; // Ordre décroissant
            }

            // En cas d'égalité, trier par heure d'arrivée
            return $a->getArrivalTimeInMinutes() - $b->getArrivalTimeInMinutes();
        });

        return $samples;
    }

    /**
     * Planifie un échantillon en trouvant technicien et équipement disponibles
     */
    private function scheduleSample(Sample $sample): void
    {
        // Trouver un technicien compatible
        $technician = $this->findCompatibleTechnician($sample);
        if (!$technician) {
            return; // Pas de technicien disponible
        }

        // Trouver un équipement compatible
        $equip = $this->findCompatibleEquipment($sample);
        if (!$equip) {
            return; // Pas d'équipement disponible
        }

        // Trouver le prochain créneau disponible commun
        $slot = $this->getNextAvailableSlot($sample, $technician, $equip);
        if (!$slot) {
            return; // Pas de créneau disponible
        }

        // Ajouter les réservations
        $startTime = $this->minutesToTime($slot['start']);
        $endTime = $this->minutesToTime($slot['end']);

        $technician->addReservation($startTime, $endTime, $sample->getId());
        $equip->addReservation($startTime, $endTime, $sample->getId());

        // Ajouter au planning
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

    /**
     * Trouve un technicien compatible avec l'échantillon
     */
    private function findCompatibleTechnician(Sample $sample): ?Technician
    {
        foreach ($this->technicians as $tech) {
            if ($tech->isCompatibleWith($sample->getType())) {
                return $tech;
            }
        }
        return null;
    }

    /**
     * Trouve un équipement compatible avec l'échantillon
     */
    private function findCompatibleEquipment(Sample $sample): ?Equipment
    {
        foreach ($this->equipment as $equip) {
            if ($equip->isCompatibleWith($sample->getType()) && $equip->isAvailable()) {
                return $equip;
            }
        }
        return null;
    }

    /**
     * Trouve le prochain créneau disponible commun pour technicien et équipement
     */
    private function getNextAvailableSlot(Sample $sample, Technician $tech, Equipment $equip): ?array
    {
        $duration = $sample->getAnalysisTime();
        $arrivalMinutes = $sample->getArrivalTimeInMinutes();

        // Commencer à partir de l'heure d'arrivée ou de l'heure de début du technicien
        $startMinutes = max($arrivalMinutes, $tech->getStartTimeInMinutes());
        $endMinutes = $tech->getEndTimeInMinutes();

        // Chercher un créneau disponible
        for ($currentStart = $startMinutes; $currentStart + $duration <= $endMinutes; $currentStart += 15) {
            $currentEnd = $currentStart + $duration;

            // Vérifier disponibilité technicien
            if (!$tech->isAvailable($currentStart, $duration)) {
                continue;
            }

            // Vérifier disponibilité équipement
            if (!$equip->isAvailableAt($currentStart, $duration)) {
                continue;
            }

            // Créneau trouvé !
            return [
                'start' => $currentStart,
                'end' => $currentEnd
            ];
        }

        return null; // Aucun créneau disponible
    }

    /**
     * Calcule les métriques du planning
     */
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

        // Trier le planning par startTime
        $sortedSchedule = $this->schedule;
        usort($sortedSchedule, function ($a, $b) {
            return $this->timeToMinutes($a['startTime']) - $this->timeToMinutes($b['startTime']);
        });

        // Calculer le temps total (de la première à la dernière analyse)
        $firstStart = $this->timeToMinutes($sortedSchedule[0]['startTime']);
        $lastEnd = $this->timeToMinutes($sortedSchedule[count($sortedSchedule) - 1]['endTime']);
        $totalTime = $lastEnd - $firstStart;

        // Compter les conflits
        $conflicts = $this->countConflicts();

        // Calculer l'efficacité (% de temps utilisé)
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

    /**
     * Compte les conflits dans le planning
     */
    private function countConflicts(): int
    {
        $conflicts = 0;

        // Vérifier les conflits entre les réservations
        foreach ($this->schedule as $i => $item1) {
            foreach ($this->schedule as $j => $item2) {
                if ($i >= $j) continue; // Éviter les doublons

                // Conflit si même technicien ou même équipement avec chevauchement
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

    /**
     * Vérifie si deux créneaux se chevauchent
     */
    private function hasOverlap(array $item1, array $item2): bool
    {
        $start1 = $this->timeToMinutes($item1['startTime']);
        $end1 = $this->timeToMinutes($item1['endTime']);
        $start2 = $this->timeToMinutes($item2['startTime']);
        $end2 = $this->timeToMinutes($item2['endTime']);

        return !($end1 <= $start2 || $start1 >= $end2);
    }

    /**
     * Convertit une heure (HH:MM) en minutes depuis minuit
     */
    private function timeToMinutes(string $time): int
    {
        [$hours, $minutes] = explode(':', $time);
        return (int)$hours * 60 + (int)$minutes;
    }

    /**
     * Convertit des minutes depuis minuit en heure (HH:MM)
     */
    private function minutesToTime(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }
}
