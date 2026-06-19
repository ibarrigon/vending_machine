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

        return $coins;
    }

    public function purchase(int $price): PurchaseResult
    {
        if ($this->retainedCash < $price) {
            throw new InsufficientFundsException();
        }

        $candidateBox = $this->changeBox->addMany(
            $this->insertedCoins->coins()
        );

        $changeAmount = $this->retainedCash - $price;

        try {
            $change = $candidateBox->withdraw($changeAmount);
            $this->changeBox = $candidateBox->removeMany($change);
            $retainedCash = 0;
        } catch (InsufficientChangeException) {
            $change = [];
            $this->changeBox = $candidateBox;
            $retainedCash = $changeAmount;
        }

        $this->insertedCoins = $this->insertedCoins->clear();
        $this->retainedCash = $retainedCash;

        return new PurchaseResult(
            change: $change,
            retainedCash: $retainedCash,
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
}
