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
    public function testItStartsWithoutInsertedCoins(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $this->assertFalse($machine->hasInsertedCoins());
    }

    public function testItAccumulatesInsertedCoins(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);
        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);
        $machine->insertCoin(Coin::FIVE_CENTS);

        $this->assertSame(55, $machine->insertedCoins()->total());
    }

    public function testItReturnsInsertedCoins(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $machine->insertCoin(Coin::ONE_EURO);

        $coins = $machine->returnCoins();

        $this->assertSame([Coin::ONE_EURO], $coins);
        $this->assertFalse($machine->hasInsertedCoins());
    }

    public function testItFailsWhenFundsAreInsufficient(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());
        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);
        $machine->purchase(100);
    }

    public function testItClearsInsertedCoinsAfterSuccessfulPurchase(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());
        $machine->insertCoin(Coin::ONE_EURO);
        $machine->purchase(100);
        $this->assertFalse($machine->hasInsertedCoins());
    }

    public function testItReturnsChangeAfterPurchase(): void
    {
        $machine = CoinMachine::load(ChangeBox::load([25 => 2, 10 => 1]), InsertedCoins::empty());
        $machine->insertCoin(Coin::ONE_EURO);
        $change = $machine->purchase(65);
        $this->assertSame(35, array_sum(array_map(fn (Coin $coin) => $coin->value, $change)));
    }

    public function testItAddsInsertedCoinsToChangeBoxAfterPurchase(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());
        $machine->insertCoin(Coin::ONE_EURO);
        $machine->purchase(100);
        $this->assertSame(1, $machine->changeBox()->quantityOf(Coin::ONE_EURO));
    }

    public function testItRefillsChangeBox(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());
        $result = $machine->refill(Coin::TEN_CENTS, 15);

        $this->assertSame(15, $result->accepted);
        $this->assertSame(15, $machine->changeBox()->quantityOf(Coin::TEN_CENTS));
    }
}
