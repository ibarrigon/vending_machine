<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Inventory\OutOfStockException;
use App\VendingMachine\Domain\Machine\State\InvalidMachineStateException;
use App\VendingMachine\Domain\Machine\State\MachineState;
use PHPUnit\Framework\TestCase;

final class VendingMachineBehaviorTest extends TestCase
{
    public function testCannotSelectProductWithoutMoney(): void
    {
        $this->expectException(InvalidMachineStateException::class);

        $machine = VendingMachineFactory::create();

        $machine->selectProduct(ProductType::SODA);
    }

    public function testCannotBuyOutOfStockProduct(): void
    {
        $this->expectException(OutOfStockException::class);

        $machine = VendingMachineFactory::withProductStock(ProductType::SODA, 0);

        $machine->insertCoin(Coin::ONE_EURO);

        $machine->selectProduct(ProductType::SODA);
    }

    public function testSuccessfulPurchaseResetsState(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        $machine->selectProduct(ProductType::SODA);

        $this->assertSame(MachineState::IDLE, $machine->state());
    }

    public function testReturnCoinsResetsState(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        $machine->returnCoins();

        $this->assertSame(MachineState::IDLE, $machine->state());
    }
}
