<?php

use PHPUnit\Framework\TestCase;

class EquipmentTest extends TestCase
{
    public function testEquipmentCreation()
    {
        $equip = new Equipment(
            'E001',
            'Blood Analyzer',
            'BLOOD',
            true
        );

        $this->assertSame('E001', $equip->getId());
        $this->assertSame('Blood Analyzer', $equip->getName());
        $this->assertSame('BLOOD', $equip->getType());
        $this->assertTrue($equip->isAvailable());
    }

    public function testSetAvailable()
    {
        $equip = new Equipment('E001', 'Blood Analyzer', 'BLOOD', true);

        $this->assertTrue($equip->isAvailable());

        $equip->setAvailable(false);
        $this->assertFalse($equip->isAvailable());
    }

    public function testCompatibilityWithSampleType()
    {
        $equipBlood = new Equipment('E1', 'Blood Analyzer', 'BLOOD', true);
        $equipUrine = new Equipment('E2', 'Urine Analyzer', 'URINE', true);

        $this->assertTrue($equipBlood->isCompatibleWith('BLOOD'));
        $this->assertFalse($equipBlood->isCompatibleWith('URINE'));

        $this->assertTrue($equipUrine->isCompatibleWith('URINE'));
        $this->assertFalse($equipUrine->isCompatibleWith('BLOOD'));
    }

    public function testAvailabilityWithReservations()
    {
        $equip = new Equipment('E1', 'Blood Analyzer', 'BLOOD', true);

        $this->assertTrue($equip->isAvailableAt(540, 30));

        $equip->addReservation('09:00', '09:30', 'S001');

        $this->assertFalse($equip->isAvailableAt(540, 30));
        $this->assertTrue($equip->isAvailableAt(510, 30));
        $this->assertTrue($equip->isAvailableAt(570, 30));
    }
}