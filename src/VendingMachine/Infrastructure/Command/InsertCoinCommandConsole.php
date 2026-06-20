<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Command;

use App\VendingMachine\Application\Client\InsertCoin\InsertCoinCommand;
use App\VendingMachine\Application\Client\InsertCoin\InsertCoinUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending:coin')]
final class InsertCoinCommandConsole extends Command
{
    public function __construct(private InsertCoinUseCase $insertCoin)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('coin', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $coin */
        $coin = $input->getArgument('coin');

        $this->insertCoin->execute(
            new InsertCoinCommand(
                machineId: 1,
                coin: \intval($coin),
            )
        );

        $output->writeln('Coin inserted');

        return Command::SUCCESS;
    }
}
