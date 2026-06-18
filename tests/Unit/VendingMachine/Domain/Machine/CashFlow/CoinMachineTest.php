<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain\Machine\State\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\ChangeBox;
use App\VendingMachine\Domain\Machine\CashFlow\CoinMachine;
use App\VendingMachine\Domain\Machine\CashFlow\InsertedCoins;
use App\VendingMachine\Domain\Machine\CashFlow\InsufficientFundsException;
use PHPUnit\Framework\TestCase;

final class CoinMachineTest extends TestCase
{
    public function testItStartsEmpty(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $this->assertFalse($machine->hasInsertedCoins());
    }

    public function testItAccumulatesCoins(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);
        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);

        $this->assertSame(50, $machine->insertedCoins()->total());
    }

    public function testItReturnsCoins(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $machine->insertCoin(Coin::ONE_EURO);

        $coins = $machine->returnCoins();

        $this->assertSame([Coin::ONE_EURO], $coins);
    }

    public function testItFailsWhenInsufficientFunds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);

        $machine->purchase(100);
    }
}
