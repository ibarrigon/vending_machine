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
    /**
     * @param list<Coin> $coins
     */
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

            $this->assertSame($initialValue, $price + $changeValue + $remain);
        } catch (InsufficientFundsException) {
            $this->assertTrue(true);
        }
    }

    /**
     * @param list<Coin> $coins
     */
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

    /**
     * @return iterable<int, array{list<Coin>, int}>
     */
    public static function coinSequencesProvider(): iterable
    {
        $cases = Coin::cases();

        for ($i = 0; $i < 50; ++$i) {
            /** @var list<Coin> $sequence */
            $sequence = array_map(
                static fn (): Coin => $cases[array_rand($cases)],
                range(1, random_int(1, 10))
            );

            yield [$sequence, random_int(0, 300)];
        }
    }

    /**
     * @param list<Coin> $coins
     */
    private function sumCoins(array $coins): int
    {
        return array_reduce(
            $coins,
            static fn (int $carry, Coin $coin): int => $carry + $coin->value,
            0
        );
    }
}
