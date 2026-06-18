<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Product;

use App\VendingMachine\Domain\Catalog\ProductType;

final readonly class RefillSlotCommand
{
    public function __construct(
        public int $machineId,
        public ProductType $product,
    ) {
    }
}
