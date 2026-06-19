<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Tests\Unit\VendingMachine\Domain\VendingMachineFactory;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Infrastructure\Persistence\Doctrine\Entity\VendingMachineRecord;
use App\VendingMachine\Infrastructure\Persistence\Doctrine\Mapper\VendingMachineMapper;

final class VendingMachinePersistenceTest extends IntegrationTestCase
{
    public function testMachineCanBePersistedAndRestored(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);
        $machine->selectProduct(ProductType::SODA);

        $record = new VendingMachineRecord();

        $mapper = new VendingMachineMapper();
        $mapper->hydrateRecord($machine, $record);

        $restored = $mapper->toDomain($record);

        $this->assertEquals($machine->slots(), $restored->slots());
        $this->assertSame($machine->state(), $restored->state());
        $this->assertEquals($machine->coinMachine()->insertedAmount(), $restored->coinMachine()->insertedAmount());
    }
}
