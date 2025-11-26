<?php

require_once __DIR__ . '/vendor/autoload.php';

$data = [
    'samples' => [
        [
            'id' => 'S001',
            'type' => 'BLOOD',
            'priority' => 'ROUTINE',
            'analysisTime' => 30,
            'arrivalTime' => '09:30',
            'patientId' => 'P001',
        ],
        [
            'id' => 'S002',
            'type' => 'BLOOD',
            'priority' => 'URGENT',
            'analysisTime' => 20,
            'arrivalTime' => '09:00',
            'patientId' => 'P002',
        ],
        [
            'id' => 'S003',
            'type' => 'BLOOD',
            'priority' => 'STAT',
            'analysisTime' => 15,
            'arrivalTime' => '08:45',
            'patientId' => 'P003',
        ],
    ],
    'technicians' => [
        [
            'id' => 'T1',
            // name volontairement manquant → fallback = id dans Scheduler
            'speciality' => 'BLOOD',
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
    ],
];

$scheduler = new Scheduler($data);
$result = $scheduler->planifyLab();

echo "EXAMPLE 2 - PRIORITIES (STAT > URGENT > ROUTINE)\n\n";
echo "SCHEDULE:\n";
echo json_encode($result['schedule'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), "\n\n";
echo "METRICS:\n";
echo json_encode($result['metrics'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), "\n";