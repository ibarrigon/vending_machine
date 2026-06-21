<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Command;

final readonly class RefillSlotCommand
{
    public function __construct(
        public int $machineId,
        public string $product,
    ) {
    }
}
