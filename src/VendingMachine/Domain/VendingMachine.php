<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\CoinMachine;
use App\VendingMachine\Domain\Machine\CashFlow\RefillResult;
use App\VendingMachine\Domain\Machine\Configuration;
use App\VendingMachine\Domain\Machine\Slot\OutOfStockException;
use App\VendingMachine\Domain\Machine\Slot\SlotState;
use App\VendingMachine\Domain\Machine\State\InvalidMachineStateException;
use App\VendingMachine\Domain\Machine\State\MachineEvent;
use App\VendingMachine\Domain\Machine\State\MachineGuards;
use App\VendingMachine\Domain\Machine\State\MachineOutcome;
use App\VendingMachine\Domain\Machine\State\MachineState;
use App\VendingMachine\Domain\Machine\State\MachineTransitionTable;

final class VendingMachine
{
    /**
     * @param array<string, SlotState> $slots
     */
    private function __construct(
        private int $id,
        private Configuration $configuration,
        private array $slots,
        private CoinMachine $coinMachine,
        private MachineState $state = MachineState::IDLE,
    ) {
    }

    /**
     * @param array<string, SlotState> $slots
     */
    public static function load(
        int $id,
        Configuration $configuration,
        array $slots,
        CoinMachine $coinMachine,
        MachineState $state = MachineState::IDLE,
    ): self {
        return new self(
            id: $id,
            configuration: $configuration,
            slots: $slots,
            coinMachine: $coinMachine,
            state: $state,
        );
    }

    public function insertCoin(Coin $coin): void
    {
        if (!MachineGuards::canInsertCoin($this->state)) {
            throw new InvalidMachineStateException();
        }

        $this->coinMachine->insertCoin($coin);

        $this->state = $this->coinMachine->hasInsertedCoins() ? MachineState::HAS_INSERTED_COINS : MachineState::IDLE;
    }

    public function selectProduct(ProductType $product): TransactionResult
    {
        if (!MachineGuards::canSelectProduct($this->state)) {
            throw new InvalidMachineStateException();
        }

        $this->state = MachineTransitionTable::eventTransition($this->state, MachineEvent::SELECT_PRODUCT);

        try {
            $slot = $this->slotByProduct($product);

            if (!$slot->canDispense()) {
                throw new OutOfStockException();
            }

            $price = $this->configuration
                ->slotConfiguration($product)
                ->price;

            $purchase = $this->coinMachine->purchase($price);

            $this->slots[$product->value] = $slot->dispense();

            $this->state = MachineTransitionTable::transition(
                $this->state,
                MachineOutcome::SUCCESS,
            );

            return new TransactionResult(
                product: $product,
                change: $purchase->change,
                retainedCash: $purchase->retainedCash,
            );
        } catch (\Throwable $e) {
            $this->state = MachineTransitionTable::transition(
                $this->state,
                MachineOutcome::FAILURE,
            );

            throw $e;
        }
    }

    /**
     * @return Coin[]
     */
    public function returnCoins(): array
    {
        if (!MachineGuards::canReturnCoins($this->state)) {
            throw new InvalidMachineStateException();
        }

        $coins = $this->coinMachine->returnCoins();

        $this->state = MachineTransitionTable::eventTransition(
            $this->state,
            MachineEvent::RETURN_COINS,
        );

        return $coins;
    }

    public function refillSlot(ProductType $product): void
    {
        $this->slots[$product->value] = $this
            ->slotByProduct($product)
            ->refill();
    }

    public function refillChange(Coin $coin, int $quantity): RefillResult 
    {
        return $this->coinMachine->refill(
            $coin,
            $quantity,
        );
    }

    public function open(): void
    {
        $this->state = MachineState::IN_MAINTENANCE;
    }

    public function isOpen(): bool
    {
        return MachineState::IN_MAINTENANCE === $this->state;
    }

    public function close(): void
    {
        $this->state = MachineState::IDLE;
    }

    public function canBeRefilled(): bool
    {
        return MachineState::IN_MAINTENANCE === $this->state;
    }

    public function isReady(): bool
    {
        return MachineState::IN_MAINTENANCE !== $this->state;
    }

    public function isInMaintenance(): bool
    {
        return MachineState::IN_MAINTENANCE === $this->state;
    }

    /**
     * @return array<string, SlotState>
     */
    public function slots(): array
    {
        return $this->slots;
    }

    public function configuration(): Configuration
    {
        return $this->configuration;
    }

    public function state(): MachineState
    {
        return $this->state;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function coinMachine(): CoinMachine
    {
        return $this->coinMachine;
    }

    public function slotByProduct(ProductType $product): SlotState
    {
        if (!isset($this->slots[$product->value])) {
            throw new ProductNotFoundException();
        }

        return $this->slots[$product->value];
    }
}
