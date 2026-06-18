<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\SelectProduct;

final class SelectProductCommand
{
    public function __construct(
        public readonly int $machineId,
        public readonly string $product,
    ) {}
}
