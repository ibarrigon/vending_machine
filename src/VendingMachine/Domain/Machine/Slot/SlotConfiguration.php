<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\Slot;

use App\VendingMachine\Domain\Catalog\ProductType;

final readonly class SlotConfiguration
{
    public function __construct(
        public ProductType $product,
        public int $price,
    ) {
    }
}
