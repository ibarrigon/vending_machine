<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Command;

use App\VendingMachine\Domain\Machine\Slot\SlotState;

final readonly class ConfigurationCommand
{
    /**
     * @param array<string, SlotState> $config
     */
    public function __construct(
        public int $machineId,
        public array $config,
    ) {
    }
}
