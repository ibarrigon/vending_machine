<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Configuration;

use App\VendingMachine\Application\Tecnician\Command\ConfigurationCommand;
use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\Machine\State\InvalidMachineStateException;
use Symfony\Component\Lock\LockFactory;

final class ConfigureMachineUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockFactory,
    ) {
    }

    public function execute(ConfigurationCommand $command): void
    {
        $lock = $this->lockFactory->createLock('machine_'.$command->machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($command->machineId);
            if (!$machine->isInMaintenance()) {
                throw new InvalidMachineStateException();
            }

            // $machine->modifyConfig($command->config);

            $this->repository->save($machine);
        } catch (\Throwable $e) {
            // TODO: Implement diferents exceptions and if machine becomes unavailable, set state as out of order
            throw $e;
        } finally {
            $lock->release();
        }
    }
}
