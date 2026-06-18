<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console;

use App\VendingMachine\Application\Client\ReturnCoins\ReturnCoinsCommand;
use App\VendingMachine\Application\Client\ReturnCoins\ReturnCoinsUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending:return')]
final class ReturnCoinsCommandConsole extends Command
{
    public function __construct(private ReturnCoinsUseCase $handler) 
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('machineId', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $coins = ($this->handler)(
            new ReturnCoinsCommand(machineId: (int) $input->getArgument('machineId')),
        );

        $output->writeln('Returned coins:');

        foreach ($coins as $coin) {
            $output->writeln($coin);
        }

        return Command::SUCCESS;
    }
}
