<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\Slot;

use App\VendingMachine\Domain\Catalog\ProductType;

final readonly class SlotState
{
    private const MAX_CAPACITY = 50;
    private const LOW_STOCK_THRESHOLD = 5;

    private function __construct(
        private ProductType $product,
        private int $quantity,
    ) {
        if ($quantity < 0 || $quantity > self::MAX_CAPACITY) {
            throw new InvalidSlotQuantityException('Inconsistent data'); // Mannipulated data???
        }
    }

    public static function load(ProductType $product, int $quantity): self
    {
        return new self($product, self::MAX_CAPACITY);
    }

    public static function filledProduct(ProductType $product): self
    {
        return new self($product, self::MAX_CAPACITY);
    }

    public function product(): ProductType
    {
        return $this->product;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function canDispense(): bool
    {
        return $this->quantity > 0;
    }

    public function dispense(): self
    {
        if (!$this->canDispense()) {
            throw new OutOfStockException();
        }

        return new self(
            $this->product,
            $this->quantity - 1,
        );
    }

    public function refill(): self
    {
        return new self(
            $this->product,
            self::MAX_CAPACITY,
        );
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
