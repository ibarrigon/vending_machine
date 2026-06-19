<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Configuration;

final readonly class ConfigurationCommand
{
    public function __construct(
        public int $machineId,
        public array $config,
    ) {
    }
}
