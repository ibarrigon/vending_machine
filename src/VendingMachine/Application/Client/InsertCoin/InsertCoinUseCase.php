<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\InsertCoin;

use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Coin\InvalidCoinException;
use Symfony\Component\Lock\LockFactory;

final readonly class InsertCoinUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockFactory,
    ) {
    }

    public function execute(InsertCoinCommand $command): void
    {
        $lock = $this->lockFactory->createLock('machine_'.$command->machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($command->machineId);

            $coin = Coin::tryFrom($command->coin);
            if (null === $coin) {
                throw new InvalidCoinException();
            }

            $machine->insertCoin($coin);

            $this->repository->save($machine);
        } catch (\Throwable $e) {
            // TODO: Implement diferents exceptions and if machine becomes unavailable, set state as out of order
            throw $e;
        } finally {
            $lock->release();
        }
    }
}
