<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Change;

use App\VendingMachine\Domain\Payment\Coin;

final readonly class RefillChangeCommand
{
    public function __construct(
        public int $machineId,
        public Coin $coin,
        public int $amount,
    ) {}
}
