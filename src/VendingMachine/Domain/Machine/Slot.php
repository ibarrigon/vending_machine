<?php

declare (strict_types=1);

namespace App\VendingMachine\Domain\Inventory;

use App\VendingMachine\Domain\Catalog\Product;

final class Slot
{
    private const MAX_CAPACITY = 50;
    private const LOW_STOCK_THRESHOLD = 5;

    public function __construct(
        private Product $product,
        private int $quantity
    ) {
        if ($quantity < 0 || $quantity > self::MAX_CAPACITY) {
            throw new InvalidSlotQuantityException();
        }
    }

    public function canDispense(): bool
    {
        return $this->quantity > 0;
    }

    public function dispense(): void
    {
        if (!$this->canDispense()) {
            throw new OutOfStockException();
        }

        $this->quantity--;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function refill(): void
    {
        $this->quantity = self::MAX_CAPACITY;
    }

    public function needsRefill(): bool
    {
        return $this->quantity <= self::LOW_STOCK_THRESHOLD;
    }

    public static function maxCapacity(): int
    {
        return self::MAX_CAPACITY;
    }    
}
