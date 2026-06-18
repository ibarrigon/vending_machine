<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Catalog;


enum ProductType: string
{
    case SODA = 'soda';
    case WATER = 'water';
    case JUICE = 'juice';

    public function selector(): string
    {
        return match ($this) {
            self::SODA => 'GET-SODA',
            self::WATER => 'GET-WATER',
            self::JUICE => 'GET-JUICE',
        };
    }

    public static function fromSelector(string $selector): ?self
    {
        return match ($selector) {
            'GET-SODA' => self::SODA,
            'GET-WATER' => self::WATER,
            'GET-JUICE' => self::JUICE,
            default => null,
        };
    }
}
