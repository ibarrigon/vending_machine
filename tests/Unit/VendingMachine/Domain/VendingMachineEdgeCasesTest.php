<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\State\InvalidMachineStateException;
use PHPUnit\Framework\TestCase;

final class VendingMachineEdgeCasesTest extends TestCase
{
    public function testMultipleCoinsAccumulation(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS); // 25
        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS); // 50
        $machine->insertCoin(Coin::TEN_CENTS); // 60
        $machine->insertCoin(Coin::FIVE_CENTS); // 65
        // We know this price. Maybe we can improbe this test with factory values?
        
        $result = $machine->selectProduct(ProductType::WATER);

        $this->assertSame(ProductType::WATER, $result->product);
    }

    public function testReturnCoinsClearsBalance(): void
    {
        $machine = VendingMachineFactory::create();
        $machine->insertCoin(Coin::ONE_EURO);
        $machine->returnCoins();

        $this->expectException(InvalidMachineStateException::class);

        $machine->selectProduct(ProductType::SODA);
    }
}
