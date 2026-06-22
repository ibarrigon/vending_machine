<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;

final readonly class ChangeBox
{
    private const MACHINE_MAX_COINS = 400;
    private const CHANGE_MAX_COINS = 200;
    private const LOW_CHANGE_THRESHOLD = 20;

    /**
     * @param array<int, int> $coins
     */
    private function __construct(
        private array $coins,
    ) {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * @param array<int, int> $coins
     */
    public static function load(array $coins): self
    {
        $normalized = [];

        foreach ($coins as $value => $qty) {
            Coin::from($value); // valida moneda

            if ($qty < 0) {
                throw new InvalidChangeBoxState('Invalid value. Negative');
            }

            $normalized[$value] = $qty;
        }

        return new self($normalized);
    }

    /**
     * @return array<int, int>
     */
    public function coins(): array
    {
        return $this->coins;
    }

    public function has(Coin $coin): bool
    {
        return ($this->coins[$coin->value] ?? 0) > 0;
    }

    public function add(Coin $coin, int $max = self::CHANGE_MAX_COINS): self
    {
        $coins = $this->coins;

        if ($this->totalCoins() >= $max) {
            throw new ChangeBoxFullException();
        }

        $coins[$coin->value] = ($coins[$coin->value] ?? 0) + 1;

        return new self($coins);
    }

    private function totalCoins(): int
    {
        return array_sum($this->coins);
    }

    /**
     * @param Coin[] $coins
     */
    public function addMany(array $coins): self
    {
        $box = $this;

        foreach ($coins as $coin) {
            $box = $box->add($coin, self::MACHINE_MAX_COINS);
        }

        return $box;
    }

    public function remove(Coin $coin): self
    {
        if (!$this->has($coin)) {
            throw new InsufficientChangeException();
        }

        $coins = $this->coins;

        --$coins[$coin->value];

        if (0 === $coins[$coin->value]) {
            unset($coins[$coin->value]);
        }

        return new self($coins);
    }

    /**
     * @param Coin[] $coins
     */
    public function removeMany(array $coins): self
    {
        $box = $this;

        foreach ($coins as $coin) {
            $box = $box->remove($coin);
        }

        return $box;
    }

    /**
     * @return list<Coin>
     */
    public function withdraw(int $amount): array
    {
        $result = [];

        $available = $this->coins;

        foreach (Coin::orderedCases() as $coin) {
            $coinValue = $coin->value;

            while ($amount >= $coinValue && ($available[$coinValue] ?? 0) > 0) {
                $amount -= $coinValue;
                --$available[$coinValue];
                $result[] = $coin;
            }
        }

        return $result;
    }

    public function refill(Coin $coin, int $quantity): ChangeBoxRefillResult
    {
        $freeCapacity = max(0, self::CHANGE_MAX_COINS - $this->totalCoins());
        $accepted = min($quantity, $freeCapacity);

        $coins = $this->coins;
        $coins[$coin->value] = ($coins[$coin->value] ?? 0) + $accepted;

        $newBox = new self($coins);

        return new ChangeBoxRefillResult(
            changeBox: $newBox,
            accepted: $accepted,
            rejected: $quantity - $accepted,
            currentQuantity: $newBox->totalCoins(),
        );
    }

    public function quantityOf(Coin $coin): int
    {
        return $this->coins[$coin->value] ?? 0;
    }

    public function fullChangeBox(): bool
    {
        return $this->totalCoins() >= self::MACHINE_MAX_COINS;
    }

    public function needChange(Coin $coin): bool
    {
        return $this->quantityOf($coin) <= self::LOW_CHANGE_THRESHOLD;
    }
}
