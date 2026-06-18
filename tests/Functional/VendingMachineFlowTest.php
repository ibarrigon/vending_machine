<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Unit\VendingMachine\Domain\VendingMachineFactory;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\InsufficientFundsException;
use PHPUnit\Framework\TestCase;

final class VendingMachineFlowTest extends TestCase
{
    public function testUserCanBuyProductWithExactChange(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        $result = $machine->selectProduct(ProductType::SODA);

        $this->assertSame(ProductType::SODA, $result->product);
        $this->assertSame([], $result->change);
    }

    public function testUserReceivesChangeWhenOverpaying(): void
    {
        $machine = VendingMachineFactory::withChange([25 => 2, 10 => 2]);

        $machine->insertCoin(Coin::ONE_EURO);

        $result = $machine->selectProduct(ProductType::WATER);

        $this->assertNotEmpty($result->change);
    }

    public function testUserGetsExceptionWhenInsufficientFunds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);

        $machine->selectProduct(ProductType::SODA);
    }

    public function testUserCanReturnCoins(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);
        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);

        $coins = $machine->returnCoins();

        $this->assertCount(2, $coins);
    }
}
