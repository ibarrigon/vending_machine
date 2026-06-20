<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console;

interface SimulatorCommand
{
    public function execute(VendingMachineCliSimulator $simulator): void;
}
