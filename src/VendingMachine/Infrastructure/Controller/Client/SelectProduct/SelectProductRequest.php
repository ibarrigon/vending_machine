<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Controller\Client\SelectProduct;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class SelectProductRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['GET-WATER', 'GET-JUICE', 'GET-SODA'])]
        public string $selector,
    ) {
    }
}
