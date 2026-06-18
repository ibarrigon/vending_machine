<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Product;

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

    public function __invoke(int $machineId): void
    {
        $lock = $this->lockFactory->createLock('machine_'.$machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($machineId);
            if (!$machine->isInMaintenance()) {
                throw new InvalidMachineStateException();
            }

            $machine->close();

            $this->repository->save($machine);
        } finally {
            $lock->release();
        }
    }
}
