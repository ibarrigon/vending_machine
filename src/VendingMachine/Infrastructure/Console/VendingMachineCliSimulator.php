<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console;

use App\VendingMachine\Application\Client\InsertCoin\InsertCoinCommand;
use App\VendingMachine\Application\Client\InsertCoin\InsertCoinUseCase;
use App\VendingMachine\Application\Client\ReturnCoins\ReturnCoinsCommand;
use App\VendingMachine\Application\Client\ReturnCoins\ReturnCoinsUseCase;
use App\VendingMachine\Application\Client\SelectProduct\SelectProductCommand;
use App\VendingMachine\Application\Client\SelectProduct\SelectProductUseCase;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;

final class VendingMachineCliSimulator
{
    public function __construct(
        private InsertCoinUseCase $insertCoin,
        private SelectProductUseCase $selectProduct,
        private ReturnCoinsUseCase $returnCoins,
        private VendingMachineScriptParser $parser,
    ) {
    }

    public function run(string $input): void
    {
        $commands = $this->parser->parse($input);

        foreach ($commands as $command) {
            $command->execute($this);
        }
    }

    // 👇 fachada pública para comandos
    public function insertCoin(Coin $coin): void
    {
        $this->insertCoin->execute(new InsertCoinCommand(1, $coin->value));
    }

    public function selectProduct(ProductType $product): void
    {
        $this->selectProduct->execute(new SelectProductCommand(1, $product->value));
    }

    public function returnCoins(): void
    {
        $this->returnCoins->execute(
            new ReturnCoinsCommand(1)
        );
    }
}
