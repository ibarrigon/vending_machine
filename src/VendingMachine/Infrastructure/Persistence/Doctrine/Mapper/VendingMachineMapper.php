<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Persistence\Doctrine\Mapper;

use App\VendingMachine\Domain\Catalog\Product;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Inventory\Slot;
use App\VendingMachine\Domain\Machine\CashFlow\ChangeBox;
use App\VendingMachine\Domain\Machine\CashFlow\CoinMachine;
use App\VendingMachine\Domain\Machine\CashFlow\InsertedCoins;
use App\VendingMachine\Domain\VendingMachine;
use App\VendingMachine\Infrastructure\Persistence\Doctrine\Entity\VendingMachineRecord;

final readonly class VendingMachineMapper
{
    public function toDomain(VendingMachineRecord $record): VendingMachine
    {
        $coinMachine = CoinMachine::load(
            $this->mapChangeToDomain($record->changeInventory()),
            $this->mapInsertedCoinsToDomain($record->insertedCoins()),
            $record->retainedCash(),
        );

        return VendingMachine::load(
            $record->id(),
            slots: $this->mapSlotsToDomain($record->slots()),
            coinMachine: $coinMachine,
        );
    }

    public function hydrateRecord(VendingMachine $machine, VendingMachineRecord $record): void
    {
        $record->setSlots($this->mapSlotsToPersistence($machine->slots()));
        $record->setChangeInventory($this->mapChangeToPersistence($machine->coinMachine()->changeBox()));
        $record->setInsertedCoins($this->mapInsertedCoinsToPersistence($machine->coinMachine()->insertedCoins()));
        $record->setRetainedCash($machine->coinMachine()->retainedCash());
    }

    /**
     * @param list<array{product: string, price: int, quantity: int}> $slots
     *
     * @return array<string, Slot>
     */
    private function mapSlotsToDomain(array $slots): array
    {
        $mapped = [];

        foreach ($slots as $slot) {
            $mapped[$slot['product']] = new Slot(
                product: new Product(ProductType::from($slot['product']), $slot['price']),
                quantity: $slot['quantity'],
            );
        }

        return $mapped;
    }

    /**
     * @param array<string, Slot> $slots
     *
     * @return list<array{product: string, price: int, quantity: int}>
     */
    private function mapSlotsToPersistence(array $slots): array
    {
        return array_values(
            array_map(
                fn (Slot $slot) => [
                    'product' => $slot->product()->type->value,
                    'price' => $slot->product()->price,
                    'quantity' => $slot->quantity(),
                ],
                $slots
            )
        );
    }

    /**
     * @param array<int, int> $data
     */
    private function mapChangeToDomain(array $data): ChangeBox
    {
        return ChangeBox::load($data);
    }

    /**
     * @param list<int> $data
     */
    private function mapInsertedCoinsToDomain(array $data): InsertedCoins
    {
        return InsertedCoins::load(
            array_map(fn (int $value) => Coin::from($value), $data)
        );
    }

    /**
     * @return list<int>
     */
    private function mapInsertedCoinsToPersistence(InsertedCoins $coins): array
    {
        return array_values(
            array_map(fn (Coin $coin) => $coin->value, $coins->coins())
        );
    }

    /**
     * @return array<int, int>
     */
    private function mapChangeToPersistence(ChangeBox $changeBox): array
    {
        return $changeBox->coins();
    }
}
