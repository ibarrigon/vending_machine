<?php

declare(strict_types=1);

namespace App\VendingMachine\Application;

final readonly class TransactionResultDTO
{
    /**
     * @param list<int> $change
     */
    public function __construct(
        public string $product,
        public array $change,
        public int $retainedCash,
    ) {
    }
}
