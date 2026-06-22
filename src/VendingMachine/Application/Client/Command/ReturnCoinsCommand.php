<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\Command;

final readonly class ReturnCoinsCommand
{
    public function __construct(public int $machineId)
    {
    }
}
