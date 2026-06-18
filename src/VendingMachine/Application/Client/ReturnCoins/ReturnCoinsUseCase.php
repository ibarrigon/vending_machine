<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\ReturnCoins;

use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use Symfony\Component\Lock\LockFactory;

final class ReturnCoinsUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockFactory,
    ) {
    }

    public function execute(ReturnCoinsCommand $command): array
    {
        $lock = $this->lockFactory->createLock('machine_'.$command->machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($command->machineId);

            $coins = $machine->returnCoins();

            $this->repository->save($machine);

            return $coins;
        } finally {
            $lock->release();
        }
    }
}
