<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console\Command;

use App\VendingMachine\Infrastructure\Console\SimulatorCommand;
use App\VendingMachine\Infrastructure\Console\VendingMachineCliSimulator;

final readonly class SelectProductSimulatorCommand implements SimulatorCommand
{
    public function __construct(private string $selector)
    {
    }

    public function execute(VendingMachineCliSimulator $simulator): string
    {
        return $simulator->selectProduct($this->selector);
    }
}
