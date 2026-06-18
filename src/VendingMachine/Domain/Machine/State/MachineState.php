<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\State;

enum MachineState: string
{
    case IDLE = 'idle';
    case HAS_INSERTED_COINS = 'has_inserted_coins';
    case DISPENSING = 'dispensing';
    case OUT_OF_SERVICE = 'out_of_service';
}
