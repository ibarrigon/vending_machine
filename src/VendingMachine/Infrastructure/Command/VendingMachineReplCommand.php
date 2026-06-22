<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Command;

use App\VendingMachine\Infrastructure\Console\VendingMachineCliSimulator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending:repl')]
final class VendingMachineReplCommand extends Command
{
    public function __construct(
        private VendingMachineCliSimulator $simulator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Vending Machine REPL started');
        $output->writeln('Commands: coins, GET-SODA (GET-<product>), RETURN, SHUTDOWN');

        $stdin = fopen('php://stdin', 'r');
        if (false === $stdin) {
            return Command::FAILURE;
        }

        while (true) {
            $line = fgets($stdin);

            if (false === $line) {
                break;
            }

            $line = trim($line);

            if ('SHUTDOWN' === $line) {
                break;
            }

            try {
                $result = $this->simulator->run($line);
                if (!empty($result)) {
                    $output->writeln($result);
                }
            } catch (\Throwable $e) {
                $output->writeln('Error: '.$e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
