<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\State\MachineState;
use PHPUnit\Framework\TestCase;

final class MachineInvariantsTest extends TestCase
{
    public function testMachineNeverHasInvalidState(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        try {
            $machine->selectProduct(ProductType::SODA);
        } catch (\Throwable) {
        }

        $this->assertContains(
            $machine->state(),
            [
                MachineState::IDLE,
                MachineState::HAS_INSERTED_COINS,
                MachineState::IN_MAINTENANCE,
            ]
        );
    }

    public function testIdleMachineHasNoCoins(): void
    {
        $machine = VendingMachineFactory::create();

        $this->assertFalse($machine->hasInsertedCoins());
    }
}
