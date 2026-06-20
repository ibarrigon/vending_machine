<?php

declare(strict_types= 1);

namespace App\VendingMachine\Infrastructure\Console;

use App\VendingMachine\Infrastructure\Console\VendingMachineCliSimulator;

interface SimulatorCommand
{
    public function execute(VendingMachineCliSimulator $simulator): void;
}
