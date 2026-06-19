<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain\Machine\State\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\ChangeBox;
use App\VendingMachine\Domain\Machine\CashFlow\CoinMachine;
use App\VendingMachine\Domain\Machine\CashFlow\InsertedCoins;
use App\VendingMachine\Domain\Machine\CashFlow\InsufficientFundsException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CoinMachinePropertyTest extends TestCase
{
    #[DataProvider('coinSequencesProvider')]
    public function testNoMoneyIsLost(array $coins, int $price): void
    {
        $machine = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        $initialValue = $this->sumCoins($coins);

        foreach ($coins as $coin) {
            $machine->insertCoin($coin);
        }

        try {
            $result = $machine->purchase($price);
            $changeValue = $this->sumCoins($result->change);
            $remain = $machine->insertedAmount();

            $this->assertGreaterThanOrEqual(0, $changeValue);
            $this->assertGreaterThanOrEqual(0, $remain);

            $this->assertEquals($initialValue, $price + $changeValue + $remain);
        } catch (InsufficientFundsException) {
            $this->assertTrue(true);
        }
    }

    #[DataProvider('coinSequencesProvider')]
    public function testPurchaseIsDeterministic(array $coins, int $price): void
    {
        $m1 = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());
        $m2 = CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty());

        foreach ($coins as $c) {
            $m1->insertCoin($c);
            $m2->insertCoin($c);
        }

        try {
            $r1 = $m1->purchase($price);
            $r2 = $m2->purchase($price);

            $this->assertEquals($r1, $r2);
        } catch (InsufficientFundsException) {
            $this->assertTrue(true);
        }
    }

    public static function coinSequencesProvider(): iterable
    {
        $coins = Coin::cases();

        for ($i = 0; $i < 50; $i++) {
            yield [
                array_map(
                    fn() => $coins[array_rand($coins)],
                    range(1, random_int(1, 10))
                ),
                random_int(0, 300),
            ];
        }
    }

    private function sumCoins(array $coins): int
    {
        return array_reduce(
            $coins,
            fn(int $carry, Coin $coin) => $carry + $coin->value,
            0
        );
    }
}
