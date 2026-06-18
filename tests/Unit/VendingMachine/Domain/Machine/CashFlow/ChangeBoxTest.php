<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain\Machine\State\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\ChangeBox;
use PHPUnit\Framework\TestCase;

final class ChangeBoxTest extends TestCase
{
    public function testItStartsEmpty(): void
    {
        $box = ChangeBox::empty();

        $this->assertSame([], $box->coins());
    }

    public function testItAddsCoin(): void
    {
        $box = ChangeBox::empty()->add(Coin::TWENTY_FIVE_CENTS);

        $this->assertSame(1, $box->quantityOf(Coin::TWENTY_FIVE_CENTS));
    }

    public function testItRemovesCoin(): void
    {
        $box = ChangeBox::load([25 => 1]);

        $box = $box->remove(Coin::TWENTY_FIVE_CENTS);

        $this->assertSame(0, $box->quantityOf(Coin::TWENTY_FIVE_CENTS));
    }

    public function testItWithdrawsExactChange(): void
    {
        $box = ChangeBox::load([25 => 1, 10 => 1]);

        $change = $box->withdraw(35);

        $this->assertSame([
            Coin::TWENTY_FIVE_CENTS,
            Coin::TEN_CENTS,
        ], $change);
    }

    public function testRefillRespectsCapacity(): void
    {
        $box = ChangeBox::empty();

        $result = $box->refill(Coin::TWENTY_FIVE_CENTS, 250);

        $this->assertSame(200, $result->accepted);
        $this->assertSame(50, $result->rejected);
    }
}
