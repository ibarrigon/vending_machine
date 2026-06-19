<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Persistence\Doctrine\Mapper;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\ChangeBox;
use App\VendingMachine\Domain\Machine\CashFlow\CoinMachine;
use App\VendingMachine\Domain\Machine\CashFlow\InsertedCoins;
use App\VendingMachine\Domain\Machine\Configuration;
use App\VendingMachine\Domain\Machine\Slot\SlotConfiguration;
use App\VendingMachine\Domain\Machine\Slot\SlotState;
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
            id: $record->id(),
            configuration: $this->mapConfigurationToDomain($record->configuration()),
            slots: $this->mapSlotsToDomain($record->slots()),
            coinMachine: $coinMachine,
        );
    }

    public function hydrateRecord(VendingMachine $machine, VendingMachineRecord $record): void
    {
        $record->setConfiguration(
            $this->mapConfigurationToPersistence($machine->configuration())
        );

        $record->setSlots(
            $this->mapSlotsToPersistence($machine->slots())
        );

        $record->setChangeInventory(
            $this->mapChangeToPersistence($machine->coinMachine()->changeBox())
        );

        $record->setInsertedCoins(
            $this->mapInsertedCoinsToPersistence($machine->coinMachine()->insertedCoins())
        );

        $record->setRetainedCash(
            $machine->coinMachine()->retainedCash()
        );
    }

    /**
     * @param list<array{product: string, quantity: int}> $slots
     *
     * @return array<string, SlotState>
     */
    private function mapSlotsToDomain(array $slots): array
    {
        $mapped = [];

        foreach ($slots as $slot) {
            $mapped[$slot['product']] = new SlotState(
                product: ProductType::from($slot['product']),
                quantity: $slot['quantity'],
            );
        }

        return $mapped;
    }

    /**
     * @param array<string, SlotState> $slots
     *
     * @return list<array{product: string, quantity: int}>
     */
    private function mapSlotsToPersistence(array $slots): array
    {
        $result = [];

        foreach ($slots as $slot) {
            $result[] = [
                'product' => $slot->product()->value,
                'quantity' => $slot->quantity(),
            ];
        }

        return $result;
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
            array_map(
                fn (int $value) => Coin::from($value),
                $data
            )
        );
    }

    /**
     * @return list<int>
     */
    private function mapInsertedCoinsToPersistence(InsertedCoins $coins): array
    {
        $result = [];

        foreach ($coins->coins() as $coin) {
            $result[] = $coin->value;
        }

        return $result;
    }

    /**
     * @return array<int, int>
     */
    private function mapChangeToPersistence(ChangeBox $changeBox): array
    {
        return $changeBox->coins();
    }

    /**
     * @param list<array{product: string, price: int}> $configuration
     */
    private function mapConfigurationToDomain(array $configuration): Configuration
    {
        $slots = [];

        foreach ($configuration as $item) {
            $slots[$item['product']] = new SlotConfiguration(
                ProductType::from($item['product']),
                $item['price'],
            );
        }

        return Configuration::load($slots);
    }

    /**
     * @return list<array{product: string, price: int}>
     */
    private function mapConfigurationToPersistence(Configuration $configuration): array
    {
        $result = [];

        foreach ($configuration->slots() as $slot) {
            $result[] = [
                'product' => $slot->product->value,
                'price' => $slot->price,
            ];
        }

        return $result;
    }
}
