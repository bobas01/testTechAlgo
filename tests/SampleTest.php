<?php

use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    public function testSampleCreation()
    {
        $sample = new Sample(
            'S001',
            'BLOOD',
            'STAT',
            30,
            '09:30',
            'P001'
        );

        $this->assertSame('S001', $sample->getId());
        $this->assertSame('BLOOD', $sample->getType());
        $this->assertSame('STAT', $sample->getPriority());
        $this->assertSame(30, $sample->getAnalysisTime());
        $this->assertSame('09:30', $sample->getArrivalTime());
        $this->assertSame('P001', $sample->getPatientId());
    }

    public function testGetArrivalTimeInMinutes()
    {
        $sample = new Sample('S001', 'BLOOD', 'STAT', 30, '09:30', 'P001');
        $this->assertSame(570, $sample->getArrivalTimeInMinutes()); // 9*60+30

        $sample2 = new Sample('S002', 'URINE', 'URGENT', 25, '08:15', 'P002');
        $this->assertSame(495, $sample2->getArrivalTimeInMinutes()); // 8*60+15
    }
}