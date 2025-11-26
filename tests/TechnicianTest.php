<?php

use PHPUnit\Framework\TestCase;

class TechnicianTest extends TestCase
{
    public function testTechnicianCreation()
    {
        $tech = new Technician(
            'T001',
            'Alice Martin',
            'BLOOD',
            '08:00',
            '17:00'
        );

        $this->assertSame('T001', $tech->getId());
        $this->assertSame('Alice Martin', $tech->getName());
        $this->assertSame('BLOOD', $tech->getSpeciality());
        $this->assertSame('08:00', $tech->getStartTime());
        $this->assertSame('17:00', $tech->getEndTime());
    }

    public function testCompatibilityWithSampleType()
    {
        $techBlood = new Technician('T001', 'Alice', 'BLOOD', '08:00', '17:00');
        $techGeneral = new Technician('T002', 'Bob', 'GENERAL', '08:00', '17:00');

        $this->assertTrue($techBlood->isCompatibleWith('BLOOD'));
        $this->assertFalse($techBlood->isCompatibleWith('URINE'));

        $this->assertTrue($techGeneral->isCompatibleWith('BLOOD'));
        $this->assertTrue($techGeneral->isCompatibleWith('URINE'));
        $this->assertTrue($techGeneral->isCompatibleWith('TISSUE'));
    }

    public function testAvailabilityWithReservations()
    {
        $tech = new Technician('T001', 'Alice', 'BLOOD', '08:00', '17:00');

        $this->assertTrue($tech->isAvailable(540, 30));

        $tech->addReservation('09:00', '09:30', 'S001');

        $this->assertFalse($tech->isAvailable(540, 30));
        $this->assertTrue($tech->isAvailable(510, 30));
        $this->assertTrue($tech->isAvailable(570, 30));
    }

    public function testRemoveReservation()
    {
        $tech = new Technician('T001', 'Alice', 'BLOOD', '08:00', '17:00');

        $tech->addReservation('09:00', '09:30', 'S001');
        $this->assertCount(1, $tech->getReservations());

        $this->assertTrue($tech->removeReservation('S001'));
        $this->assertCount(0, $tech->getReservations());

        $this->assertFalse($tech->removeReservation('S999'));
    }
}