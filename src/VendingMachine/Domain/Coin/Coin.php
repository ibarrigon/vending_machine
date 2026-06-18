<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Coin;

enum Coin: int
{
    case FIVE_CENTS = 5;
    case TEN_CENTS = 10;
    case TWENTY_FIVE_CENTS = 25;
    case ONE_EURO = 100;

    public static function orderedCases(): array
    {
        return [
            Coin::ONE_EURO,
            Coin::TWENTY_FIVE_CENTS,
            Coin::TEN_CENTS,
            Coin::FIVE_CENTS,
        ];
    }
}
