<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\ReturnCoins;

final class ReturnCoinsCommand
{
    public function __construct(
        public readonly int $machineId,
    ) {
    }
}
