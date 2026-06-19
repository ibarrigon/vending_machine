<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Transformer;

final readonly class OutputCentsTransformer
{
    public function transform(int $coin): string
    {
        return number_format($coin / 100, 2, '.', '');
    }

    /**
     * @param list<int> $coinsList
     *
     * @return list<string>
     */
    public function transformList(array $coinsList): array
    {
        return array_map(fn (int $coin): string => $this->transform($coin), $coinsList);
    }
}
