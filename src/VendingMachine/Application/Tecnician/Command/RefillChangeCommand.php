<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Command;

final readonly class RefillChangeCommand
{
    public function __construct(
        public int $machineId,
        public int $coin,
        public int $amount,
    ) {
    }
}
