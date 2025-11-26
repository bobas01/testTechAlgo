<?php

require_once __DIR__ . '/vendor/autoload.php';

$data = [
    'samples' => [
        [
            'id' => 'S001',
            'type' => 'BLOOD',
            'priority' => 'URGENT',
            'analysisTime' => 30,
            'arrivalTime' => '09:00',
            'patientId' => 'P001',
        ],
        [
            'id' => 'S002',
            'type' => 'URINE',
            'priority' => 'URGENT',
            'analysisTime' => 30,
            'arrivalTime' => '09:00',
            'patientId' => 'P002',
        ],
        [
            'id' => 'S003',
            'type' => 'BLOOD',
            'priority' => 'ROUTINE',
            'analysisTime' => 45,
            'arrivalTime' => '09:15',
            'patientId' => 'P003',
        ],
        [
            'id' => 'S004',
            'type' => 'TISSUE',
            'priority' => 'ROUTINE',
            'analysisTime' => 30,
            'arrivalTime' => '09:30',
            'patientId' => 'P004',
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
            'name' => 'Bob URINE',
            'speciality' => 'URINE',
            'startTime' => '08:00',
            'endTime' => '17:00',
        ],
        [
            'id' => 'T3',
            // GENERAL pour tester la polyvalence
            'name' => 'Charlie GENERAL',
            'speciality' => 'GENERAL',
            'startTime' => '08:00',
            'endTime' => '17:00',
        ],
    ],
    'equipment' => [
        [
            'id' => 'E1',
            'name' => 'Blood Analyzer',
            'type' => 'BLOOD',
            'available' => true,
        ],
        [
            'id' => 'E2',
            'name' => 'Urine Analyzer',
            'type' => 'URINE',
            'available' => true,
        ],
        [
            'id' => 'E3',
            // name manquant → fallback = id
            'type' => 'TISSUE',
            'available' => true,
        ],
    ],
];

$scheduler = new Scheduler($data);
$result = $scheduler->planifyLab();

echo "EXAMPLE 3 - PARALLELISM & MULTIPLE TYPES\n\n";
echo "SCHEDULE:\n";
echo json_encode($result['schedule'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), "\n\n";
echo "METRICS:\n";
echo json_encode($result['metrics'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), "\n";