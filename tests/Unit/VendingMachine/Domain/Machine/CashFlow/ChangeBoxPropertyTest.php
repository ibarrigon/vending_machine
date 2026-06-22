<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain\Machine\State\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\ChangeBox;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ChangeBoxPropertyTest extends TestCase
{
    /**
     * @param array<int, int> $input
     */
    #[DataProvider('cases')]
    public function testConservationOfCoins(array $input): void
    {
        $box = ChangeBox::load($input);

        $this->assertSame($input, $box->coins());
    }

    /**
     * @return iterable<int, array{array<int,int>}>
     */
    public static function cases(): iterable
    {
        yield [[25 => 2, 10 => 1]];
        yield [[25 => 0]];
    }

    /**
     * @param array<int, int> $initial
     */
    #[DataProvider('randomBoxesProvider')]
    public function testNoNegativeQuantities(array $initial, int $amount): void
    {
        $box = ChangeBox::load($initial);
        $result = $box->withdraw($amount);
        $box = $box->removeMany($result);

        if (empty($box->coins())) {
            $this->addToAssertionCount(1);
            return;
        }

        foreach ($box->coins() as $qty) {
            $this->assertGreaterThanOrEqual(0, $qty);
        }
    }

    /**
     * @param array<int, int> $initial
     */
    #[DataProvider('randomBoxesProvider')]
    public function testWithdrawIsDeterministic(array $initial, int $amount): void
    {
        $box1 = ChangeBox::load($initial);
        $box2 = ChangeBox::load($initial);

        $r1 = $box1->withdraw($amount);
        $r2 = $box2->withdraw($amount);

        $this->assertEquals($r1, $r2);
    }

    /**
     * @return iterable<int, array{array<int,int>, int}>
     */
    public static function randomBoxesProvider(): iterable
    {
        for ($i = 0; $i < 50; ++$i) {
            yield [
                self::randomBox(),
                random_int(0, 50),
            ];
        }
    }

    /**
     * @return array<int, int>
     */
    private static function randomBox(): array
    {
        $coins = array_map(
            fn (Coin $coin): int => $coin->value,
            Coin::cases()
        );

        $box = [];

        foreach ($coins as $coin) {
            $box[$coin] = random_int(0, 20);
        }

        return $box;
    }

    public function testWithdrawReturnsPartialChangeWhenExactChangeIsImpossible(): void
    {
        $box = ChangeBox::load([
            25 => 1,
            10 => 0,
            5 => 0,
            100 => 0,
        ]);

        $change = $box->withdraw(30);

        $this->assertEquals(
            [Coin::TWENTY_FIVE_CENTS],
            $change
        );
    }

    public function testWithdrawNeverReturnsMoreThanRequested(): void
    {
        $box = ChangeBox::load([
            100 => 10,
            25 => 10,
            10 => 10,
            5 => 10,
        ]);

        $change = $box->withdraw(37);

        $returned = array_sum(
            array_map(
                fn(Coin $coin) => $coin->value,
                $change
            )
        );

        $this->assertLessThanOrEqual(37, $returned);
    }

    public function testWithdrawCannotInventCoins(): void
    {
        $box = ChangeBox::load([
            100 => 1,
            25 => 2,
            10 => 0,
            5 => 0,
        ]);

        $change = $box->withdraw(500);

        $this->assertLessThanOrEqual(
            1,
            count(array_filter(
                $change,
                fn(Coin $coin) => $coin === Coin::ONE_EURO
            ))
        );
    }
}
