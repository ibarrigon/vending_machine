<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\InsufficientFundsException;
use App\VendingMachine\Domain\Machine\State\MachineState;
use PHPUnit\Framework\TestCase;

final class MachineInvariantsTest extends TestCase
{
    public function testIdleMachineCannotHaveInsertedCoins(): void
    {
        $machine = VendingMachineFactory::create();

        $this->assertSame(
            MachineState::IDLE,
            $machine->state()
        );

        $this->assertFalse(
            $machine->coinMachine()->hasInsertedCoins()
        );
    }

    public function testMachineWithInsertedCoinsCannotBeIdle(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        $this->assertSame(
            MachineState::HAS_INSERTED_COINS,
            $machine->state()
        );

        $this->assertTrue(
            $machine->coinMachine()->hasInsertedCoins()
        );
    }

    public function testReturningCoinsRestoresIdleInvariant(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        $machine->returnCoins();

        $this->assertSame(
            MachineState::IDLE,
            $machine->state()
        );

        $this->assertFalse(
            $machine->coinMachine()->hasInsertedCoins()
        );
    }

    public function testSuccessfulPurchaseRestoresIdleInvariant(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        $machine->selectProduct(ProductType::JUICE);

        $this->assertSame(
            MachineState::IDLE,
            $machine->state()
        );

        $this->assertFalse(
            $machine->coinMachine()->hasInsertedCoins()
        );
    }

    public function testFailedPurchasePreservesInsertedCoinsInvariant(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);

        try {
            $machine->selectProduct(ProductType::SODA);
        } catch (InsufficientFundsException) {
        }

        $this->assertSame(
            MachineState::HAS_INSERTED_COINS,
            $machine->state()
        );

        $this->assertTrue(
            $machine->coinMachine()->hasInsertedCoins()
        );
    }

    public function testMachineInMaintenanceIsFlaggedCorrectly(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->open();

        $this->assertSame(
            MachineState::IN_MAINTENANCE,
            $machine->state()
        );

        $this->assertTrue(
            $machine->isInMaintenance()
        );

        $this->assertTrue(
            $machine->canBeRefilled()
        );
    }
}
