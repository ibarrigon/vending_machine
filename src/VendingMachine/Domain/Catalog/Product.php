<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Catalog;

final class Product
{
    public function __construct(
        public readonly ProductType $type,
        public readonly int $price,
    ) {
    }
}
