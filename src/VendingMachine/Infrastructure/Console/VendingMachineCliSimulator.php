<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console;

use App\VendingMachine\Application\Client\Command\InsertCoinCommand;
use App\VendingMachine\Application\Client\Command\ReturnCoinsCommand;
use App\VendingMachine\Application\Client\Command\SelectProductCommand;
use App\VendingMachine\Application\Client\InsertCoin\InsertCoinUseCase;
use App\VendingMachine\Application\Client\ReturnCoins\ReturnCoinsUseCase;
use App\VendingMachine\Application\Client\SelectProduct\SelectProductUseCase;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Infrastructure\Transformer\OutputReturnCoinsTransformer;
use App\VendingMachine\Infrastructure\Transformer\OutputSelectProductTransformer;

final class VendingMachineCliSimulator
{
    public function __construct(
        private InsertCoinUseCase $insertCoin,
        private SelectProductUseCase $selectProduct,
        private ReturnCoinsUseCase $returnCoins,
        private VendingMachineScriptParser $parser,
        private OutputSelectProductTransformer $selectProductTransformer,
        private OutputReturnCoinsTransformer $outputReturn,
    ) {
    }

    /**
     * @return list<string>
     */
    public function run(string $input): array
    {
        $configs = $this->parser->parse($input);

        $output = [];
        foreach ($configs as $config) {
            $output[] = $config->execute($this);
        }

        return $output;
    }

    // 👇 fachada pública para comandos
    public function insertCoin(Coin $coin): string
    {
        $this->insertCoin->execute(new InsertCoinCommand(1, $coin->value));

        return '';
    }

    public function selectProduct(string $selector): string
    {
        return $this->selectProductTransformer->transform(
            $this->selectProduct->execute(new SelectProductCommand(1, $selector))
        );
    }

    public function returnCoins(): string
    {
        return $this->outputReturn->transform($this->returnCoins->execute(new ReturnCoinsCommand(1)));
    }
}
