<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Inventory\OutOfStockException;
use App\VendingMachine\Domain\Inventory\Slot;
use App\VendingMachine\Domain\Machine\CashFlow\CoinMachine;
use App\VendingMachine\Domain\Machine\CashFlow\RefillResult;
use App\VendingMachine\Domain\Machine\State\InvalidMachineStateException;
use App\VendingMachine\Domain\Machine\State\MachineEvent;
use App\VendingMachine\Domain\Machine\State\MachineGuards;
use App\VendingMachine\Domain\Machine\State\MachineOutcome;
use App\VendingMachine\Domain\Machine\State\MachineState;
use App\VendingMachine\Domain\Machine\State\MachineTransitionTable;

final class VendingMachine
{
    private function __construct(
        private int $id,
        /** @var array<string, Slot> */
        private array $slots,
        private CoinMachine $coinMachine,
        private MachineState $state = MachineState::IDLE,
    ) {
    }

    /**
     * @param array<string, Slot> $slots
     */
    public static function load(
        int $id,
        array $slots,
        CoinMachine $coinMachine,
    ): self {
        return new self($id, $slots, $coinMachine);
    }

    public function insertCoin(Coin $coin): void
    {
        if (!MachineGuards::canInsertCoin($this->state)) {
            throw new InvalidMachineStateException();
        }

        $this->coinMachine->insertCoin($coin);

        $this->state = match (true) {
            $this->coinMachine->hasInsertedCoins() > 0 => MachineState::HAS_INSERTED_COINS,
            default => MachineState::IDLE,
        };
    }

    public function selectProduct(ProductType $type): TransactionResult
    {
        if (!MachineGuards::canSelectProduct($this->state)) {
            throw new InvalidMachineStateException();
        }

        $this->state = MachineTransitionTable::eventTransition($this->state, MachineEvent::SELECT_PRODUCT);

        try {
            $slot = $this->slotByProduct($type);

            if (!$slot->canDispense()) {
                throw new OutOfStockException();
            }

            $result = $this->coinMachine->purchase($slot->product()->price);

            $slot->dispense();

            $this->state = MachineTransitionTable::transition($this->state, MachineOutcome::SUCCESS);

            return new TransactionResult($type, $result->change, $result->retainedCash);
        } catch (\Throwable $e) {
            // FAILURE transition
            $this->state = MachineTransitionTable::transition($this->state, MachineOutcome::FAILURE);

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

        $this->state = MachineTransitionTable::eventTransition($this->state, MachineEvent::RETURN_COINS);

        return $coins;
    }

    /**
     * @return array<string, Slot>
     */
    public function slots(): array
    {
        return $this->slots;
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

    public function refillSlot(ProductType $product): void
    {
        $this->slotByProduct($product)->refill();
    }

    public function refillChange(Coin $coin, int $quantity): RefillResult
    {
        return $this->coinMachine->refill($coin, $quantity);
    }

    public function open(): void
    {
        $this->state = MachineState::IN_MAINTENANCE;
    }

    public function close(): void
    {
        $this->state = MachineState::IDLE;
    }

    public function canBeRefilled(): bool
    {
        return MachineState::IN_MAINTENANCE === $this->state;
    }

    public function isInMaintenance(): bool
    {
        return MachineState::IN_MAINTENANCE === $this->state;
    }

    private function slotByProduct(ProductType $product): Slot
    {
        return $this->slots[$product->value];
    }
}
