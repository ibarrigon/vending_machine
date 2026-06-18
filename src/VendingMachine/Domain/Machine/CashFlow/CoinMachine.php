<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;

final class CoinMachine
{
    private function __construct(
        private ChangeBox $changeBox,
        private InsertedCoins $insertedCoins,
    ) {}

    public static function load(
        ChangeBox $changeBox,
        InsertedCoins $insertedCoins,
    ): self {
        return new self($changeBox, $insertedCoins);
    }

    public function insertCoin(Coin $coin): void
    {
        $this->insertedCoins = $this->insertedCoins->insert($coin);
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

    /**
     * @return Coin[]
     */
    public function purchase(int $price): array
    {
        $paid = $this->insertedCoins->total();

        if ($paid < $price) {
            throw new InsufficientFundsException();
        }

        $candidateBox = $this->changeBox->addMany($this->insertedCoins->coins());
        $change = $candidateBox->withdraw($paid - $price);
        $this->changeBox = $candidateBox->removeMany($change);

        $this->insertedCoins = $this->insertedCoins->clear();

        return $change;
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
}
