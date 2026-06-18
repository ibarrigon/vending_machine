<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\InsufficientFundsException;
use PHPUnit\Framework\TestCase;

final class VendingMachineDoubleSpendingTest extends TestCase
{
    public function testItCannotSpendSameCoinsTwice(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);

        $machine->selectProduct(ProductType::WATER);

        $this->expectException(InsufficientFundsException::class);

        $machine->selectProduct(ProductType::WATER);
    }
}
