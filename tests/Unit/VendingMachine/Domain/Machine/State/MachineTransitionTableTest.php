<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain\Machine\State;

use App\VendingMachine\Domain\Machine\State\MachineEvent;
use App\VendingMachine\Domain\Machine\State\MachineOutcome;
use App\VendingMachine\Domain\Machine\State\MachineState;
use App\VendingMachine\Domain\Machine\State\MachineTransitionTable;
use PHPUnit\Framework\TestCase;

final class MachineTransitionTableTest extends TestCase
{
    public function testIdleToHasInsertedCoins(): void
    {
        $state = MachineTransitionTable::eventTransition(
            MachineState::IDLE,
            MachineEvent::INSERT_COIN,
        );

        $this->assertSame(MachineState::HAS_INSERTED_COINS, $state);
    }

    public function testHasCoinsToDispensing(): void
    {
        $state = MachineTransitionTable::eventTransition(
            MachineState::HAS_INSERTED_COINS,
            MachineEvent::SELECT_PRODUCT,
        );

        $this->assertSame(MachineState::DISPENSING, $state);
    }

    public function testSuccessReturnsToIdle(): void
    {
        $state = MachineTransitionTable::transition(
            MachineState::DISPENSING,
            MachineOutcome::SUCCESS,
        );

        $this->assertSame(MachineState::IDLE, $state);
    }

    public function testFailureReturnsToHasCoins(): void
    {
        $state = MachineTransitionTable::transition(
            MachineState::DISPENSING,
            MachineOutcome::FAILURE,
        );

        $this->assertSame(MachineState::HAS_INSERTED_COINS, $state);
    }
}
