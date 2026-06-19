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
    public function __construct(private SelectProductUseCase $selectProduct)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('machineId', InputArgument::OPTIONAL, '1')
            ->addArgument('product', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $machineId */
        $machineId = $input->getArgument('machineId');
        /** @var string $product */
        $product = $input->getArgument('product');

        $result = $this->selectProduct->execute(
            new SelectProductCommand(
                machineId: \intval($machineId),
                product: $product,
            )
        );

        $output->writeln($result->product);

        foreach ($result->change as $coin) {
            $output->writeln("Change: {$coin}");
        }

        return Command::SUCCESS;
    }
}
