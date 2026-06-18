<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Product;

use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use Symfony\Component\Lock\LockFactory;

final class RefillSlotUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockFactory,
    ) {}

    public function __invoke(RefillSlotCommand $command): void
    {
        $lock = $this->lockFactory->createLock('machine_' . $command->machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($command->machineId);
            $machine->refillSlot($command->product);

            $this->repository->save($machine);
        } finally {
            $lock->release();
        }
    }
}
