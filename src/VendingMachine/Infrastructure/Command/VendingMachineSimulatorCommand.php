<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $script */
        $script = $input->getArgument('script');

        $this->simulator->run($script);

        return Command::SUCCESS;
    }
}
