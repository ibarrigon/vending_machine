<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;

final class CoinMachine
{
    private function __construct(
        private ChangeBox $changeBox,
        private InsertedCoins $insertedCoins,
        private int $pendingBalance = 0,
    ) {
    }

    public static function load(
        ChangeBox $changeBox,
        InsertedCoins $insertedCoins,
        int $pendingBalance = 0,
    ): self {
        return new self($changeBox, $insertedCoins, $pendingBalance);
    }

    public function insertCoin(Coin $coin): void
    {
        $this->insertedCoins = $this->insertedCoins->insert($coin);
        $this->pendingBalance += $coin->value;
    }

    /**
     * @return Coin[]
     */
    public function returnCoins(): array
    {
        $coins = $this->insertedCoins->coins();
        $this->pendingBalance = 0;
        $this->insertedCoins = $this->insertedCoins->clear();

        return $coins;
    }

    /**
     * @return Coin[]
     */
    public function purchase(int $price): array
    {
        if ($this->pendingBalance < $price) {
            throw new InsufficientFundsException();
        }

        $candidateBox = $this->changeBox->addMany($this->insertedCoins->coins());

        try {
            $change = $candidateBox->withdraw($this->pendingBalance - $price);
        } catch (InsufficientChangeException) {
            // 👇 CLAVE: no rompemos estado, dejamos saldo pendiente
            throw new InsufficientChangeException();
        }

        $this->changeBox = $candidateBox->removeMany($change);

        $this->insertedCoins = $this->insertedCoins->clear();
        $this->pendingBalance = 0;

        return $change;
    }

    public function insertedAmount(): int
    {
        return $this->pendingBalance;
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
