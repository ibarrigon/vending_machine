<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Controller\Client;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class SelectProductRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(['GET-WATER', 'GET-JUICE', 'GET-SODA'])]
        public string $product,
    ) {
    }
}
