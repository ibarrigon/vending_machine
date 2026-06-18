<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\SelectProduct;

final readonly class SelectProductResponse
{
    public function __construct(
        public string $product,
        public array $change,
    ) {
    }
}
