<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Console;

use App\VendingMachine\Application\Tecnician\Change\RefillChangeUseCase;
use App\VendingMachine\Application\Tecnician\CloseMachineUseCase;
use App\VendingMachine\Application\Tecnician\Command\RefillChangeCommand;
use App\VendingMachine\Application\Tecnician\Command\RefillSlotCommand;
use App\VendingMachine\Application\Tecnician\OpenMachineUseCase;
use App\VendingMachine\Application\Tecnician\Product\RefillSlotUseCase;
use App\VendingMachine\Application\Tecnician\ResetCreditUseCase;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending:fill')]
final class FillCommand extends Command
{
    private const MACHINE = 1;

    public function __construct(
        private RefillSlotUseCase $refillSlot,
        private RefillChangeUseCase $refillChange,
        private ResetCreditUseCase $reset,
        private OpenMachineUseCase $open,
        private CloseMachineUseCase $close,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->open->execute(self::MACHINE);
            $this->reset->execute(self::MACHINE);
            $this->refillSlot->execute(new RefillSlotCommand(self::MACHINE, ProductType::WATER->value));
            $this->refillSlot->execute(new RefillSlotCommand(self::MACHINE, ProductType::SODA->value));
            $this->refillSlot->execute(new RefillSlotCommand(self::MACHINE, ProductType::JUICE->value));
            foreach (Coin::cases() as $coin) {
                $this->refillChange->execute(new RefillChangeCommand(self::MACHINE, $coin->value, 50));
            }
            $this->close->execute(self::MACHINE);

            $output->writeln('Machine filled successfully');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('Reset machine failure: '.$e->getMessage());
            $output->writeln('Error: '.$e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
