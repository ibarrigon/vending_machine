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
    private const MAX_COINS = 200;

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
        $this->assertFalse($machine->hasInsertedCoins());
    }

    public function testItFailsWhenInsufficientFunds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);
        $machine->purchase(100);
    }

    public function testPurchaseWithExactAmount(): void
    {
        $machine = CoinMachine::load(
            ChangeBox::empty(),
            InsertedCoins::empty(),
        );

        $machine->insertCoin(Coin::ONE_EURO);

        $result = $machine->purchase(100);

        $this->assertSame([], $result->change);
        $this->assertSame(0, $result->retainedCash);
        $this->assertSame(0, $machine->retainedCash());
        $this->assertFalse($machine->hasInsertedCoins());
    }

    public function testPurchaseReturnsChangeWhenAvailable(): void
    {
        $changeBox = ChangeBox::load([
            Coin::TWENTY_FIVE_CENTS->value => 4,
        ]);

        $machine = CoinMachine::load(
            $changeBox,
            InsertedCoins::empty(),
        );

        $machine->insertCoin(Coin::ONE_EURO);

        $result = $machine->purchase(75);

        $this->assertCount(1, $result->change);
        $this->assertSame(
            Coin::TWENTY_FIVE_CENTS,
            $result->change[0]
        );

        $this->assertSame(0, $result->retainedCash);
    }

    public function testPurchaseKeepsCreditWhenChangeCannotBeReturned(): void
    {
        $machine = CoinMachine::load(
            ChangeBox::empty(),
            InsertedCoins::empty(),
        );

        $machine->insertCoin(Coin::ONE_EURO);

        $result = $machine->purchase(75);

        $this->assertSame([], $result->change);
        $this->assertSame(25, $result->retainedCash);

        $this->assertSame(25, $machine->retainedCash());
    }

    public function testPurchaseClearsInsertedCoins(): void
    {
        $machine = CoinMachine::load(
            ChangeBox::empty(),
            InsertedCoins::empty(),
        );

        $machine->insertCoin(Coin::ONE_EURO);

        $machine->purchase(75);

        $this->assertFalse($machine->hasInsertedCoins());
        $this->assertSame(
            0,
            $machine->insertedCoins()->total()
        );
    }

    public function testRetainedCreditIsUsedInFollowingPurchase(): void
    {
        $machine = CoinMachine::load(
            ChangeBox::empty(),
            InsertedCoins::empty(),
        );

        $machine->insertCoin(Coin::ONE_EURO);

        $machine->purchase(75);

        $this->assertSame(25, $machine->retainedCash());

        $machine->insertCoin(Coin::TWENTY_FIVE_CENTS);

        $this->assertSame(50, $machine->retainedCash());
    }

    public function testResetRetainedCash(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty(), 50);

        $machine->resetRetainedCash();

        $this->assertSame(0, $machine->retainedCash());
    }

    public function testRefillUpdatesMachineChangeBox(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $result = $machine->refill(Coin::TEN_CENTS, 5);

        $this->assertSame(5, $result->accepted);
        $this->assertSame(5, $machine->changeBox()->quantityOf(Coin::TEN_CENTS));
    }

    public function testResetEmptyAllCreditInsideMachine(): void
    {
        $machine = CoinMachine::load(ChangeBox::load([Coin::TEN_CENTS->value => 100]), InsertedCoins::empty(), 50);
        $machine->insertCoin(Coin::ONE_EURO);
        $this->assertNotEquals(0, $machine->retainedCash());
        $this->assertNotEquals(0, $machine->changeBox()->quantityOf(Coin::TEN_CENTS));
        $this->assertTrue($machine->hasInsertedCoins());

        $machine->reset();

        $this->assertSame(0, $machine->retainedCash());
        $this->assertSame(0, $machine->changeBox()->quantityOf(Coin::TEN_CENTS));
        $this->assertFalse($machine->hasInsertedCoins());
    }

    public function testItRefillsTheChangeBox(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $result = $machine->refill(Coin::TWENTY_FIVE_CENTS, 5);

        $this->assertSame(Coin::TWENTY_FIVE_CENTS, $result->coin);

        $this->assertSame(5, $result->accepted);
        $this->assertSame(0, $result->rejected);
        $this->assertSame(5, $result->currentQuantity);

        $this->assertSame(5, $machine->changeBox()->coins()[Coin::TWENTY_FIVE_CENTS->value]);
    }

    public function testRefillUpdatesInternalChangeBox(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $machine->refill(Coin::TEN_CENTS, 3);

        $machine->refill(Coin::TEN_CENTS, 2);

        $this->assertSame(5, $machine->changeBox()->coins()[Coin::TEN_CENTS->value]);
    }

    public function testRefillAddsCoinsToChangeBox(): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $result = $machine->refill(Coin::TEN_CENTS, 5);

        $this->assertSame(5, $result->accepted);
        $this->assertSame(0, $result->rejected);
        $this->assertSame(5, $result->currentQuantity);

        $this->assertSame(5, $machine->changeBox()->coins()[Coin::TEN_CENTS->value]);
    }

    public function testRefillPartiallyAcceptsCoinsWhenCapacityIsReached(): void
    {
        $machine = CoinMachine::load(
            ChangeBox::empty(),
            InsertedCoins::empty(),
        );

        $machine->refill(Coin::TEN_CENTS, self::MAX_COINS - 3);
        $result = $machine->refill(Coin::TEN_CENTS, 10);

        $this->assertSame(3, $result->accepted);
        $this->assertSame(7, $result->rejected);
        $this->assertSame(self::MAX_COINS, $result->currentQuantity);
    }
}
