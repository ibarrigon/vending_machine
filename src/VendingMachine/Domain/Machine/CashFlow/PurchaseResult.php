<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;

final readonly class PurchaseResult
{
    /**
     * @param Coin[] $change
     */
    public function __construct(
        public array $change,
        public int $retainedCash,
    ) {
    }
}
