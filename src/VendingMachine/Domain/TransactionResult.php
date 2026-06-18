<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;

final class TransactionResult
{
    public function __construct(
        public ProductType $product,
        public array $change
    ) {}
}
