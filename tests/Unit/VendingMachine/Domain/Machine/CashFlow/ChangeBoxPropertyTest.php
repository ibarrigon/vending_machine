<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain\Machine\State\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\ChangeBox;
use App\VendingMachine\Domain\Machine\CashFlow\InsufficientChangeException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ChangeBoxPropertyTest extends TestCase
{
    #[DataProvider('cases')]
    public function testConservationOfCoins(array $input): void
    {
        $box = ChangeBox::load($input);
        $this->assertSame($input, $box->coins());
    }

    public static function cases(): array
    {
        return [
            [[25 => 2, 10 => 1]],
            [[25 => 0]],
        ];
    }

    #[DataProvider('randomBoxesProvider')]
    public function testNoNegativeQuantities(array $initial, int $amount): void
    {
        $box = ChangeBox::load($initial);

        try {
            $result = $box->withdraw($amount);
            $box = $box->removeMany($result);

            foreach ($box->coins() as $qty) {
                $this->assertGreaterThanOrEqual(0, $qty);
            }
        } catch (InsufficientChangeException) {
            $this->assertTrue(true);
        }
    }

    #[DataProvider('randomBoxesProvider')]
    public function testWithdrawIsDeterministic(array $initial, int $amount): void
    {
        $box1 = ChangeBox::load($initial);
        $box2 = ChangeBox::load($initial);

        try {
            $r1 = $box1->withdraw($amount);
            $r2 = $box2->withdraw($amount);

            $this->assertEquals($r1, $r2);
        } catch (InsufficientChangeException) {
            $this->assertTrue(true);
        }
    }

    public static function randomBoxesProvider(): iterable
    {
        for ($i = 0; $i < 50; $i++) {
            yield [
                self::randomBox(),
                random_int(0, 500),
            ];
        }
    }

    private static function randomBox(): array
    {
        $coins = array_map(fn(Coin $coin) => $coin->value, Coin::cases());
        $box = [];

        foreach ($coins as $coin) {
            $box[$coin] = random_int(0, 20);
        }

        return $box;
    }

    private function totalCoins(array $box): int
    {
        return array_sum($box);
    }
}
