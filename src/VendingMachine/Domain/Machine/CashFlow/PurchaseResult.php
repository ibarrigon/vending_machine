<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

final readonly class PurchaseResult
{
    public function __construct(
        public array $change,
        public int $retainedCash,
    ) {
    }
}
