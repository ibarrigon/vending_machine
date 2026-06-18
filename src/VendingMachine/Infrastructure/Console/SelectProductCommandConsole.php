<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console;

use App\VendingMachine\Application\Client\SelectProduct\SelectProductCommand;
use App\VendingMachine\Application\Client\SelectProduct\SelectProductUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending:select')]
final class SelectProductCommandConsole extends Command
{
    public function __construct(private SelectProductUseCase $handler)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('machineId', InputArgument::REQUIRED)
            ->addArgument('product', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = ($this->handler)(
            new SelectProductCommand(
                machineId: (int) $input->getArgument('machineId'),
                product: $input->getArgument('product'),
            )
        );

        $output->writeln($result->product);

        foreach ($result->change as $coin) {
            $output->writeln("Change: {$coin}");
        }

        return Command::SUCCESS;
    }
}
