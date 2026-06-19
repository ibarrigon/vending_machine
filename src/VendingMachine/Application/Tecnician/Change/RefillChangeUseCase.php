<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Change;

use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Machine\CashFlow\RefillResult;
use App\VendingMachine\Domain\Machine\State\InvalidMachineStateException;
use Symfony\Component\Lock\LockFactory;

final readonly class RefillChangeUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockFactory,
    ) {
    }

    public function __invoke(RefillChangeCommand $command): RefillResult
    {
        $lock = $this->lockFactory->createLock('machine_'.$command->machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($command->machineId);
            if (!$machine->canBeRefilled()) {
                throw new InvalidMachineStateException();
            }
            $result = $machine->refillChange(Coin::from($command->coin), $command->amount);
            $this->repository->save($machine);

            return $result;
        } catch (\Throwable $e) {
            // TODO: Implement diferents exceptions and if machine becomes unavailable, set state as out of order
            throw $e;
        } finally {
            $lock->release();
        }
    }
}
