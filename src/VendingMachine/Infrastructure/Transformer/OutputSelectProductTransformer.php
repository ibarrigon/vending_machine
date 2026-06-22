<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Transformer;

use App\VendingMachine\Application\TransactionResultDTO;

final readonly class OutputSelectProductTransformer
{
    public function __construct(private OutputCentsTransformer $transformer)
    {
    }

    public function transform(TransactionResultDTO $dto): string
    {
        $output = [
            'product: '.$dto->product,
        ];

        if (!empty($dto->change)) {
            $output[] = 'returned coins: '.implode(', ', $this->transformer->transformList($dto->change));
        }

        if (!empty($dto->retainedCash)) {
            $output[] = 'retained change: '.$this->transformer->transform($dto->retainedCash);
        }

        return implode(',', $output);
    }
}
