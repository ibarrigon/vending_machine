<?php

declare(strict_types= 1);

namespace App\VendingMachine\Domain\Machine\State;

enum MachineEvent: string
{
    case INSERT_COIN = 'insert_coin';
    case SELECT_PRODUCT = 'select_product';
    case RETURN_COINS = 'return_coins';
    case SERVICE_MODE = 'service_mode';
}
