<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console\Commands;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Infrastructure\Console\SimulatorCommand;
use App\VendingMachine\Infrastructure\Console\VendingMachineCliSimulator;

final readonly class SelectProductSimulatorCommand implements SimulatorCommand
{
    public function __construct(private ProductType $product) {}

    public function execute(int $machineId, VendingMachineCliSimulator $simulator): void
    {
        $simulator->selectProduct($machineId, $this->product);
    }
}
