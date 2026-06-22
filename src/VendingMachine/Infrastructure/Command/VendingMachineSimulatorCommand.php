<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Command;

use App\VendingMachine\Infrastructure\Console\VendingMachineCliSimulator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending:sim')]
final class VendingMachineSimulatorCommand extends Command
{
    public function __construct(
        private VendingMachineCliSimulator $simulator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('script', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $script */
        $script = $input->getArgument('script');

        try {
            $output->writeln('Command: '.$script);
            $output->writeln($this->simulator->run($script));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('Error: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
