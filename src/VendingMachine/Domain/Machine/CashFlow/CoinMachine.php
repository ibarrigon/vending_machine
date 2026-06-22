<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;

final class CoinMachine
{
    private function __construct(
        private ChangeBox $changeBox,
        private InsertedCoins $insertedCoins,
        private int $retainedCash = 0,
    ) {
    }

    public static function load(
        ChangeBox $changeBox,
        InsertedCoins $insertedCoins,
        int $retainedCash = 0,
    ): self {
        return new self($changeBox, $insertedCoins, $retainedCash);
    }

    public static function empty(): self
    {
        return new self(ChangeBox::empty(), InsertedCoins::empty(), 0);
    }

    public function insertCoin(Coin $coin): void
    {
        $this->insertedCoins = $this->insertedCoins->insert($coin);
        $this->retainedCash += $coin->value;
    }

    /**
     * @return Coin[]
     */
    public function returnCoins(): array
    {
        $coins = $this->insertedCoins->coins();
        $this->insertedCoins = $this->insertedCoins->clear();
        foreach ($coins as $coin) {
            $this->retainedCash -= $coin->value;
        }

        $this->retainedCash = max($this->retainedCash, 0); // Fallback by errors, but never can be negative

        return $coins;
    }

    public function purchase(int $price): PurchaseResult
    {
        if ($this->retainedCash < $price) {
            throw new InsufficientFundsException();
        }

        $candidateBox = $this->changeBox->addMany($this->insertedCoins->coins());
        $changeAmount = $this->retainedCash - $price;
        $change = $candidateBox->withdraw($changeAmount);
        $returnedAmount = array_sum(array_map(static fn (Coin $coin) => $coin->value, $change));
        $this->changeBox = $candidateBox->removeMany($change);
        $this->insertedCoins = $this->insertedCoins->clear();
        $this->retainedCash = $changeAmount - $returnedAmount;

        return new PurchaseResult(
            change: $change,
            retainedCash: $this->retainedCash,
        );
    }

    public function insertedAmount(): int
    {
        return $this->retainedCash;
    }

    public function hasInsertedCoins(): bool
    {
        return !$this->insertedCoins->isEmpty();
    }

    public function changeBox(): ChangeBox
    {
        return $this->changeBox;
    }

    public function insertedCoins(): InsertedCoins
    {
        return $this->insertedCoins;
    }

    public function refill(Coin $coin, int $quantity): RefillResult
    {
        $result = $this->changeBox->refill($coin, $quantity);
        $this->changeBox = $result->changeBox;

        return new RefillResult(
            coin: $coin,
            accepted: $result->accepted,
            rejected: $result->rejected,
            currentQuantity: $result->currentQuantity,
        );
    }

    public function retainedCash(): int
    {
        return $this->retainedCash;
    }

    public function resetRetainedCash(): void
    {
        $this->retainedCash = 0;
    }

    public function reset(): void
    {
        $this->changeBox = ChangeBox::empty();
        $this->insertedCoins = InsertedCoins::empty();
        $this->resetRetainedCash();
    }

    public function fullChangeBox(): bool
    {
        return $this->changeBox->fullChangeBox();
    }

    public function hasCoins(Coin $coin): bool
    {
        return $this->changeBox->has($coin);
    }
}
