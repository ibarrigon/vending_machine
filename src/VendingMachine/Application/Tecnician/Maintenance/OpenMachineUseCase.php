<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Product;

use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use Symfony\Component\Lock\LockFactory;

final class OpenMachineUseCase
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
            $machine->open();

            $this->repository->save($machine);
        } finally {
            $lock->release();
        }
    }
}
