<?php

declare(strict_types=1);

namespace App\VendingMachine\Application;

use Symfony\Component\Lock\LockFactory;

final readonly class MachineTransactionService
{
    public function __construct(
        private LockFactory $lockFactory,
        private VendingMachineRepositoryInterface $repository,
    ) {}

    public function run(int $machineId, callable $action): mixed
    {
        $lock = $this->lockFactory->createLock('machine_' . $machineId);

        $lock->acquire(true);

        try {
            $machine = $this->repository->get($machineId);

            $result = $action($machine);

            $this->repository->save($machine);

            return $result;
        } finally {
            $lock->release();
        }
    }
}
