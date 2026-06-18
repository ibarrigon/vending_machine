<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Inventory\OutOfStockException;
use App\VendingMachine\Domain\Machine\CashFlow\InsufficientFundsException;
use App\VendingMachine\Domain\Machine\State\InvalidMachineStateException;
use App\VendingMachine\Domain\Machine\State\MachineState;
use PHPUnit\Framework\TestCase;

final class VendingMachineTest extends TestCase
{
    public function testItStartsInIdleState(): void
    {
        $machine = VendingMachineFactory::create();

        $this->assertSame(MachineState::IDLE, $machine->state());
    }

    public function testItMovesToHasMoneyAfterInsertingCoin(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        $this->assertSame(MachineState::HAS_INSERTED_COINS, $machine->state());
    }

    public function testItVendsProduct(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        $result = $machine->selectProduct(ProductType::SODA);

        $this->assertSame(ProductType::SODA, $result->product);
    }

    public function testItReturnsToIdleAfterSuccessfulPurchase(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);
        $machine->selectProduct(ProductType::SODA);

        $this->assertSame(MachineState::IDLE, $machine->state());
    }

    public function testItReturnsCoins(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        $coins = $machine->returnCoins();

        $this->assertCount(1, $coins);
        $this->assertSame(MachineState::IDLE, $machine->state());
    }

    public function testItCannotSelectProductWithoutMoney(): void
    {
        $this->expectException(InvalidMachineStateException::class);

        $machine = VendingMachineFactory::create();

        $machine->selectProduct(ProductType::SODA);
    }

    public function testItFailsWhenOutOfStock(): void
    {
        $this->expectException(OutOfStockException::class);

        $machine = VendingMachineFactory::withProductStock(ProductType::SODA, 0);

        $machine->insertCoin(Coin::ONE_EURO);

        $machine->selectProduct(ProductType::SODA);
    }

    public function testItFailsWhenInsufficientFunds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);

        $machine->selectProduct(ProductType::SODA);
    }
}
