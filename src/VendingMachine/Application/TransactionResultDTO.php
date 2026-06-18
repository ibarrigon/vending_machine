<?php

declare(strict_types=1);

namespace App\VendingMachine\Application;

final class TransactionResultDTO
{
    public function __construct(
        public readonly string $product,
        public readonly array $change,
    ) {}
}
