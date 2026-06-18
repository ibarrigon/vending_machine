<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\State;

final class MachineGuards
{
    public static function canInsertCoin(MachineState $state): bool
    {
        return $state !== MachineState::OUT_OF_SERVICE;
    }

    public static function canSelectProduct(MachineState $state): bool
    {
        return $state === MachineState::HAS_INSERTED_COINS;
    }

    public static function canReturnCoins(MachineState $state): bool
    {
        return in_array($state, [
            MachineState::HAS_INSERTED_COINS,
            MachineState::DISPENSING,
        ], true);
    }
}
