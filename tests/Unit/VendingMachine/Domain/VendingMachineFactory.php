<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\ChangeBox;
use App\VendingMachine\Domain\Machine\CashFlow\CoinMachine;
use App\VendingMachine\Domain\Machine\CashFlow\InsertedCoins;
use App\VendingMachine\Domain\Machine\Configuration;
use App\VendingMachine\Domain\Machine\Slot\SlotState;
use App\VendingMachine\Domain\VendingMachine;

final class VendingMachineFactory
{
    public static function create(): VendingMachine
    {
        return VendingMachine::load(
            id: 1,
            configuration: Configuration::factorySettings(),
            slots: self::defaultSlots(),
            coinMachine: CoinMachine::load(
                ChangeBox::empty(),
                InsertedCoins::empty()
            ),
        );
    }

    public static function withProductStock(ProductType $productType, int $quantity): VendingMachine
    {
        return VendingMachine::load(
            id: 1,
            configuration: Configuration::factorySettings(),
            slots: self::slot($productType, $quantity),
            coinMachine: CoinMachine::load(
                ChangeBox::empty(),
                InsertedCoins::empty()
            ),
        );
    }

    /**
     * @return array<string, SlotState>
     */
    private static function slot(ProductType $product, int $amount): array
    {
        return [
            $product->value => new SlotState(
                product: $product,
                quantity: $amount
            ),
        ];
    }

    /**
     * @return array<string, SlotState>
     */
    private static function defaultSlots(): array
    {
        return [
            ProductType::SODA->value => new SlotState(ProductType::SODA, 10),
            ProductType::WATER->value => new SlotState(ProductType::WATER, 10),
            ProductType::JUICE->value => new SlotState(ProductType::JUICE, 10),
        ];
    }

    /**
     * @param array<int, int> $change
     */
    public static function withChange(array $change): VendingMachine
    {
        $box = ChangeBox::empty();

        foreach ($change as $coinValue => $qty) {
            for ($i = 0; $i < $qty; ++$i) {
                $box = $box->add(Coin::from($coinValue));
            }
        }

        return VendingMachine::load(
            id: 1,
            configuration: Configuration::factorySettings(),
            slots: self::defaultSlots(),
            coinMachine: CoinMachine::load(
                $box,
                InsertedCoins::empty()
            ),
        );
    }
}
