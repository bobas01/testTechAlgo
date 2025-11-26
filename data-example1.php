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
    ],
    'technicians' => [
        [
            'id' => 'T1',
            'name' => 'Alice BLOOD',
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

echo "EXAMPLE 1 - SIMPLE CASE\n\n";
echo "SCHEDULE:\n";
echo json_encode($result['schedule'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), "\n\n";
echo "METRICS:\n";
echo json_encode($result['metrics'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), "\n";