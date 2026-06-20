<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Command;

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
    public function __construct(private SelectProductUseCase $selectProduct)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('selector', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $selector */
        $selector = $input->getArgument('selector');

        $result = $this->selectProduct->execute(
            new SelectProductCommand(
                machineId: 1,
                selector: $selector,
            )
        );

        $output->writeln($result->product);

        foreach ($result->change as $coin) {
            $output->writeln("Change: {$coin}");
        }

        return Command::SUCCESS;
    }
}
