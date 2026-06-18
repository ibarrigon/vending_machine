<?php

declare(strict_types=1);

use App\Tests\Unit\VendingMachine\Domain\VendingMachineFactory;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Infrastructure\Persistence\Doctrine\Entity\VendingMachineRecord;
use App\VendingMachine\Infrastructure\Persistence\Doctrine\Mapper\VendingMachineMapper;
use PHPUnit\Framework\TestCase;

final class VendingMachinePersistenceTest extends TestCase
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

        $this->assertSame($machine->state(), $restored->state());
    }
}
