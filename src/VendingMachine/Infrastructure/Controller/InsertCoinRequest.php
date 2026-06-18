<?php

declare(strict_types= 1);

namespace App\VendingMachine\Infrastructure\Controller;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class InsertCoinRequest
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Choice([5, 10, 25, 100])]        
        public int $coin,
    ) {}
}
