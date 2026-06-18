<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\State;

enum MachineOutcome: string
{
    case SUCCESS = 'success';
    case FAILURE = 'failure';
}
