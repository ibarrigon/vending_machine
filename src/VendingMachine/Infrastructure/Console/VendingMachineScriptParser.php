<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console;

use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Infrastructure\Console\Command\InsertCoinSimulatorCommand;
use App\VendingMachine\Infrastructure\Console\Command\ReturnCoinsSimulatorCommand;
use App\VendingMachine\Infrastructure\Console\Command\SelectProductSimulatorCommand;

final readonly class VendingMachineScriptParser
{
    /**
     * @return list<SimulatorCommand>
     */
    public function parse(string $input): array
    {
        $tokens = array_map('trim', explode(',', $input));

        $commands = [];
        foreach ($tokens as $token) {
            $commands[] = $this->map($token);
        }

        return $commands;
    }

    private function map(string $token): SimulatorCommand
    {
        if ($this->isCoin($token)) {
            return new InsertCoinSimulatorCommand($this->mapCoin((float) $token));
        }

        if ('RETURN-COIN' === $token) {
            return new ReturnCoinsSimulatorCommand();
        }

        try {
            return new SelectProductSimulatorCommand($token);
        } catch (\Throwable) {
            throw new \InvalidArgumentException("Unknown token: $token");
        }
    }

    private function mapCoin(float $value): Coin
    {
        $coinValue = \intval(\round($value * 100));

        $coin = Coin::tryFrom($coinValue);

        if (null === $coin) {
            throw new \InvalidArgumentException('Invalid coin');
        }

        return $coin;
    }

    private function isCoin(string $token): bool
    {
        return is_numeric($token);
    }
}
