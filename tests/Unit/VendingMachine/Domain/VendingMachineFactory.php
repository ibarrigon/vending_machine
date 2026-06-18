<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\Product;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Inventory\Slot;
use App\VendingMachine\Domain\Machine\CashFlow\ChangeBox;
use App\VendingMachine\Domain\Machine\CashFlow\CoinMachine;
use App\VendingMachine\Domain\Machine\CashFlow\InsertedCoins;
use App\VendingMachine\Domain\VendingMachine;

final class VendingMachineFactory
{
    public static function create(): VendingMachine
    {
        return VendingMachine::load(
            1,
            slots: self::defaultSlots(),
            coinMachine: CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty()),
        );
    }

    public static function withProductStock(ProductType $productType, int $quantity): VendingMachine
    {
        return VendingMachine::load(
            1,
            slots: self::slot($productType, $quantity),
            coinMachine: CoinMachine::load(ChangeBox::empty(), InsertedCoins::empty()),
        );
    }

    private static function slot(ProductType $product, int $amount): array
    {
        return [
            $product->value => new Slot(new Product($product, 150), $amount),
        ];
    }

    private static function defaultSlots(): array
    {
        return [
            ProductType::SODA->value => new Slot(new Product(ProductType::SODA, 150), 1),
            ProductType::WATER->value => new Slot(new Product(ProductType::WATER, 65), 10),
            ProductType::JUICE->value => new Slot(new Product(ProductType::JUICE, 100), 10),
        ];
    }

    public static function withChange(array $change): VendingMachine
    {
        $box = ChangeBox::empty();

        foreach ($change as $coinValue => $qty) {
            for ($i = 0; $i < $qty; ++$i) {
                $box = $box->add(Coin::from($coinValue));
            }
        }

        return VendingMachine::load(
            1,
            slots: self::defaultSlots(),
            coinMachine: CoinMachine::load($box, InsertedCoins::empty()),
        );
    }
}
