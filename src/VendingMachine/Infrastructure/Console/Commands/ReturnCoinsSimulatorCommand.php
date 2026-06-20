<?php

declare(strict_types= 1);

namespace App\VendingMachine\Infrastructure\Console\Commands;

use App\VendingMachine\Infrastructure\Console\SimulatorCommand;
use App\VendingMachine\Infrastructure\Console\VendingMachineCliSimulator;

final readonly class ReturnCoinsSimulatorCommand implements SimulatorCommand
{
    public function execute(int $machineId, VendingMachineCliSimulator $simulator): void
    {
        $simulator->returnCoins($machineId);
    }
}
