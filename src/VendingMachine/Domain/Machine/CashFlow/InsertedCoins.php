<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;

final readonly class InsertedCoins
{
    /** @param Coin[] $coins */
    public function __construct(
        private array $coins = [],
    ) {
    }

    /**
     * @param Coin[] $coins
     */
    public static function load(array $coins): self
    {
        return new self($coins);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function insert(Coin $coin): self
    {
        return new self([
            ...$this->coins,
            $coin,
        ]);
    }

    /**
     * @return Coin[]
     */
    public function coins(): array
    {
        return $this->coins;
    }

    public function total(): int
    {
        return array_reduce(
            $this->coins,
            fn (int $sum, Coin $coin) => $sum + $coin->value,
            0
        );
    }

    public function clear(): self
    {
        return new self([]);
    }

    public function isEmpty(): bool
    {
        return [] === $this->coins;
    }
}
