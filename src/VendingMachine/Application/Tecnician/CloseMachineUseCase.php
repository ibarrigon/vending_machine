<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician;

use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\Machine\State\InvalidMachineStateException;
use Symfony\Component\Lock\LockFactory;

final class CloseMachineUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockFactory,
    ) {
    }

    public function execute(int $machineId): void
    {
        $lock = $this->lockFactory->createLock('machine_'.$machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($machineId);
            if (!$machine->isInMaintenance()) {
                throw new InvalidMachineStateException('Machine cannot be closed.');
            }

            $machine->close();

            $this->repository->save($machine);
        } catch (\Throwable $e) {
            // TODO: Implement diferents exceptions and if machine becomes unavailable, set state as out of order
            throw $e;
        } finally {
            $lock->release();
        }
    }
}
