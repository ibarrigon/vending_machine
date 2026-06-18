<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

final readonly class ChangeBoxRefillResult
{
    public function __construct(
        public ChangeBox $changeBox,
        public int $accepted,
        public int $rejected,
        public int $currentQuantity,
    ) {}
}
