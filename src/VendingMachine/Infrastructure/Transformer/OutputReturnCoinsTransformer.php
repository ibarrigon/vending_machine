<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Transformer;

final readonly class OutputReturnCoinsTransformer
{
    public function __construct(private OutputCentsTransformer $transformer)
    {
    }

    /**
     * @param list<int> $coins
     */
    public function transform(array $coins): string
    {
        if (empty($coins)) {
            return 'Nothing to return';
        }

        return implode(', ', $this->transformer->transformList($coins));
    }
}
