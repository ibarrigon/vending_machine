<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Change;

final readonly class RefillChangeReponse
{
    public function __construct(
        public int $coin,
        public int $accepted,
        public int $rejected,
        public int $currentQuantity,
    ) {
    }
}
