<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

use App\VendingMachine\Domain\Coin\Coin;

final readonly class PurchaseResult
{
    public function __construct(
        /** @var list<Coin> $change */
        public array $change,
        public int $retainedCash,
    ) {
    }
}
