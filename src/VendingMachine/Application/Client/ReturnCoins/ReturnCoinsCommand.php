<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\ReturnCoins;

final readonly class ReturnCoinsCommand
{
    public function __construct(
        public int $machineId,
    ) {
    }
}
