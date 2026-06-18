<?php

declare(strict_types=1);

namespace App\VendingMachine\Application;

use App\VendingMachine\Domain\VendingMachine;

interface VendingMachineRepositoryInterface
{
    public function get(int $id): VendingMachine;

    public function save(VendingMachine $machine): void;
}
