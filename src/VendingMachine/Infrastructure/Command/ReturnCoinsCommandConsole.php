<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Command;

use App\VendingMachine\Application\Client\Command\ReturnCoinsCommand;
use App\VendingMachine\Application\Client\ReturnCoins\ReturnCoinsUseCase;
use App\VendingMachine\Infrastructure\Transformer\OutputReturnCoinsTransformer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending:return')]
final class ReturnCoinsCommandConsole extends Command
{
    public function __construct(
        private ReturnCoinsUseCase $returnCoint,
        private OutputReturnCoinsTransformer $transformer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // TODO: If we have a machine farm, we can use a parameter to add machine id
        $machineId = 1;

        $output->write(
            $this->transformer->transform(
                $this->returnCoint->execute(new ReturnCoinsCommand(machineId: \intval($machineId)))
            )
        );

        return Command::SUCCESS;
    }
}
