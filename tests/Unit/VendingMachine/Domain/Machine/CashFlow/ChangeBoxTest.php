<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain\Machine\State\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\ChangeBox;
use App\VendingMachine\Domain\Machine\CashFlow\InsufficientChangeException;
use PHPUnit\Framework\TestCase;

final class ChangeBoxTest extends TestCase
{
    public function testItStartsEmpty(): void
    {
        $box = ChangeBox::empty();

        $this->assertSame([], $box->coins());
    }

    public function testItAddsACoin(): void
    {
        $box = ChangeBox::empty();

        $box = $box->add(Coin::TWENTY_FIVE_CENTS);

        $this->assertSame(
            1,
            $box->quantityOf(Coin::TWENTY_FIVE_CENTS)
        );
    }

    public function testItRemovesACoin(): void
    {
        $box = ChangeBox::load([
            25 => 1,
        ]);

        $box = $box->remove(Coin::TWENTY_FIVE_CENTS);

        $this->assertSame(
            0,
            $box->quantityOf(Coin::TWENTY_FIVE_CENTS)
        );
    }

    public function testItFailsWhenRemovingUnavailableCoin(): void
    {
        $this->expectException(InsufficientChangeException::class);

        ChangeBox::empty()->remove(Coin::TWENTY_FIVE_CENTS);
    }

    public function testItWithdrawsExactChange(): void
    {
        $box = ChangeBox::load([
            25 => 1,
            10 => 1,
        ]);

        $change = $box->withdraw(35);

        $this->assertSame(
            [
                Coin::TWENTY_FIVE_CENTS,
                Coin::TEN_CENTS,
            ],
            $change
        );
    }

    public function testItReturnsEmptyArrayWhenZeroChangeIsRequested(): void
    {
        $box = ChangeBox::empty();

        $this->assertSame(
            [],
            $box->withdraw(0)
        );
    }

    public function testItFailsWhenChangeCannotBeBuilt(): void
    {
        $this->expectException(
            InsufficientChangeException::class
        );

        ChangeBox::load([
            25 => 1,
        ])->withdraw(35);
    }

    public function testRefillAcceptsAllCoinsWhenCapacityIsAvailable(): void
    {
        $box = ChangeBox::empty();

        $result = $box->refill(
            Coin::TWENTY_FIVE_CENTS,
            10
        );

        $this->assertSame(10, $result->accepted);
        $this->assertSame(0, $result->rejected);
        $this->assertSame(10, $result->currentQuantity);
    }

    public function testRefillRejectsExcessCoinsWhenCapacityIsReached(): void
    {
        $box = ChangeBox::empty();

        $result = $box->refill(
            Coin::TWENTY_FIVE_CENTS,
            250
        );

        $this->assertSame(200, $result->accepted);
        $this->assertSame(50, $result->rejected);
        $this->assertSame(200, $result->currentQuantity);
    }

    public function testItDetectsLowChangeThreshold(): void
    {
        $box = ChangeBox::load([
            25 => 20,
        ]);

        $this->assertTrue(
            $box->needChange(Coin::TWENTY_FIVE_CENTS)
        );
    }

    public function testItDoesNotRequestRefillWhenAboveThreshold(): void
    {
        $box = ChangeBox::load([
            25 => 21,
        ]);

        $this->assertFalse(
            $box->needChange(Coin::TWENTY_FIVE_CENTS)
        );
    }
}
