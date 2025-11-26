<?php

use PHPUnit\Framework\TestCase;

class SchedulerTest extends TestCase
{
    public function testPriorityOrderingAndNoConflicts()
    {
        $data = [
            'samples' => [
                [
                    'id' => 'S1',
                    'type' => 'BLOOD',
                    'priority' => 'ROUTINE',
                    'analysisTime' => 30,
                    'arrivalTime' => '09:30',
                    'patientId' => 'P1',
                ],
                [
                    'id' => 'S2',
                    'type' => 'BLOOD',
                    'priority' => 'URGENT',
                    'analysisTime' => 20,
                    'arrivalTime' => '09:00',
                    'patientId' => 'P2',
                ],
                [
                    'id' => 'S3',
                    'type' => 'BLOOD',
                    'priority' => 'STAT',
                    'analysisTime' => 15,
                    'arrivalTime' => '08:45',
                    'patientId' => 'P3',
                ],
            ],
            'technicians' => [
                [
                    'id' => 'T1',
                    'name' => 'Alice',
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

        $schedule = $result['schedule'];
        $metrics = $result['metrics'];

        $this->assertCount(3, $schedule);

        $this->assertSame('STAT',   $schedule[0]['priority']);
        $this->assertSame('URGENT', $schedule[1]['priority']);
        $this->assertSame('ROUTINE',$schedule[2]['priority']);

        $this->assertSame(0, $metrics['conflicts']);
        $this->assertGreaterThan(0, $metrics['totalTime']);
        $this->assertGreaterThan(0, $metrics['efficiency']);
    }
}