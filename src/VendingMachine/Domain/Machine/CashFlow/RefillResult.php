<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;

final readonly class RefillResult
{
    public function __construct(
        public Coin $coin,
        public int $accepted,
        public int $rejected,
        public int $currentQuantity,
    ) {
    }
}
