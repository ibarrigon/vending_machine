<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console\Command;

use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Infrastructure\Console\SimulatorCommand;
use App\VendingMachine\Infrastructure\Console\VendingMachineCliSimulator;

final readonly class InsertCoinSimulatorCommand implements SimulatorCommand
{
    public function __construct(private Coin $coin)
    {
    }

    public function execute(VendingMachineCliSimulator $simulator): string
    {
        return $simulator->insertCoin($this->coin);
    }
}
