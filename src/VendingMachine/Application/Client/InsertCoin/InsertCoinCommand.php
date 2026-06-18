<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\InsertCoin;


final class InsertCoinCommand
{
    public function __construct(
        public readonly int $machineId,
        public readonly int $coin,
    ) {}
}
