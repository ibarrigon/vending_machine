<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\State\MachineState;
use PHPUnit\Framework\TestCase;

final class VendingMachineInvariantsTest extends TestCase
{
    public function testMachineStateIsAlwaysValid(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        try {
            $machine->selectProduct(ProductType::SODA);
        } catch (\Throwable) {
        }

        $state = $machine->state();

        $validStates = [
            MachineState::IDLE,
            MachineState::HAS_INSERTED_COINS,
            MachineState::IN_MAINTENANCE,
        ];

        $this->assertTrue(
            in_array($state, $validStates, true)
        );
    }
}
