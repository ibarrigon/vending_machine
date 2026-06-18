<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\State;

final class MachineTransitionTable
{
    private const OUTCOME_TRANSITIONS = [
        MachineState::DISPENSING->value => [
            MachineOutcome::SUCCESS->value => MachineState::IDLE,
            MachineOutcome::FAILURE->value => MachineState::HAS_INSERTED_COINS,
        ],
    ];

    private const EVENT_TRANSITIONS = [
        MachineState::IDLE->value => [
            MachineEvent::INSERT_COIN->value => MachineState::HAS_INSERTED_COINS,
        ],

        MachineState::HAS_INSERTED_COINS->value => [
            MachineEvent::SELECT_PRODUCT->value => MachineState::DISPENSING,
            MachineEvent::RETURN_COINS->value => MachineState::IDLE,
        ],
    ];

    public static function transition(MachineState $state, MachineOutcome $eventOrOutcome): MachineState
    {
        return self::OUTCOME_TRANSITIONS[$state->value][$eventOrOutcome->value]
            ?? throw new InvalidMachineStateTransitionException();
    }

    public static function eventTransition(MachineState $state, MachineEvent $event): MachineState
    {
        return self::EVENT_TRANSITIONS[$state->value][$event->value]
        ?? throw new InvalidMachineStateTransitionException();
    }
}
