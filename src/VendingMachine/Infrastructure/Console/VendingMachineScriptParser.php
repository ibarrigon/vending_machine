<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console;

use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Infrastructure\Console\Commands\InsertCoinSimulatorCommand;
use App\VendingMachine\Infrastructure\Console\Commands\ReturnCoinsSimulatorCommand;
use App\VendingMachine\Infrastructure\Console\Commands\SelectProductSimulatorCommand;

final class VendingMachineScriptParser
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

        if ($product = ProductType::fromSelector($token)) {
            return new SelectProductSimulatorCommand($product);
        }

        if ('RETURN' === $token) {
            return new ReturnCoinsSimulatorCommand();
        }

        throw new \InvalidArgumentException("Unknown token: $token");
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
