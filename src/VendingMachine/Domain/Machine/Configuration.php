<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Machine\Slot\SlotConfiguration;
use App\VendingMachine\Domain\ProductNotFoundException;

final readonly class Configuration
{
    /**
     * @param array<string, SlotConfiguration> $slots
     */
    private function __construct(
        private array $slots,
    ) {
    }

    /**
     * @param array<string, SlotConfiguration> $slots
     */
    public static function load(array $slots): self
    {
        return new self($slots);
    }

    public static function factorySettings(): self
    {
        return new self([
            ProductType::WATER->value => new SlotConfiguration(ProductType::WATER, 65),
            ProductType::JUICE->value => new SlotConfiguration(ProductType::JUICE, 100),
            ProductType::SODA->value => new SlotConfiguration(ProductType::SODA, 150),
        ]);
    }

    /**
     * @return array<string, SlotConfiguration>
     */
    public function slots(): array
    {
        return $this->slots;
    }

    public function slotConfiguration(ProductType $product): SlotConfiguration
    {
        if (!isset($this->slots[$product->value])) {
            throw new ProductNotFoundException();
        }

        return $this->slots[$product->value];
    }
}
