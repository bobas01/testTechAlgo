<?php

require_once __DIR__ . '/vendor/autoload.php';

$data = [
    'samples' => [
        // Exemple 1 : simple, un seul échantillon
        [
            'id' => 'S001',
            'type' => 'BLOOD',
            'priority' => 'URGENT',
            'analysisTime' => 30,
            'arrivalTime' => '09:00',
            'patientId' => 'P001',
        ],

        // Exemple 2 : plusieurs priorités
        [
            'id' => 'S002',
            'type' => 'BLOOD',
            'priority' => 'STAT',
            'analysisTime' => 20,
            'arrivalTime' => '08:30',
            'patientId' => 'P002',
        ],
        [
            'id' => 'S003',
            'type' => 'URINE',
            'priority' => 'ROUTINE',
            'analysisTime' => 40,
            'arrivalTime' => '10:00',
            'patientId' => 'P003',
        ],

        // Exemple 3 : parallélisme possible
        [
            'id' => 'S004',
            'type' => 'BLOOD',
            'priority' => 'URGENT',
            'analysisTime' => 45,
            'arrivalTime' => '09:15',
            'patientId' => 'P004',
        ],
        [
            'id' => 'S005',
            'type' => 'BLOOD',
            'priority' => 'ROUTINE',
            'analysisTime' => 30,
            'arrivalTime' => '09:20',
            'patientId' => 'P005',
        ],
    ],

    'technicians' => [
        [
            'id' => 'T1',
            'name' => 'Alice BLOOD',
            'speciality' => 'BLOOD',
            'startTime' => '08:00',
            'endTime' => '17:00',
        ],
        [
            'id' => 'T2',
            // name volontairement manquant pour tester le fallback
            'speciality' => 'URINE',
            'startTime' => '08:00',
            'endTime' => '17:00',
        ],
        [
            'id' => 'T3',
            'name' => 'Charlie GENERAL',
            'speciality' => 'GENERAL',
            'startTime' => '08:00',
            'endTime' => '17:00',
        ],
    ],

    'equipment' => [
        [
            'id' => 'E1',
            'name' => 'Analyseur Sang A',
            'type' => 'BLOOD',
            'available' => true,
        ],
        [
            'id' => 'E2',
            // name manquant pour tester le fallback
            'type' => 'URINE',
            'available' => true,
        ],
    ],
];

$scheduler = new Scheduler($data);
$result = $scheduler->planifyLab();

echo "SCHEDULE:\n";
echo json_encode($result['schedule'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n\n";

echo "METRICS:\n";
echo json_encode($result['metrics'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n";