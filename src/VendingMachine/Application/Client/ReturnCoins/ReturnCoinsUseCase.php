<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\ReturnCoins;

use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\Coin\Coin;
use Symfony\Component\Lock\LockFactory;

final readonly class ReturnCoinsUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockFactory,
    ) {
    }

    /**
     * @return list<int>
     */
    public function execute(ReturnCoinsCommand $command): array
    {
        $lock = $this->lockFactory->createLock('machine_'.$command->machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($command->machineId);

            $coins = $machine->returnCoins();

            $this->repository->save($machine);

            return array_values(
                array_map(fn (Coin $coin) => $coin->value, $coins)
            );
        } catch (\Throwable $e) {
            // TODO: Implement diferents exceptions and if machine becomes unavailable, set state as out of order
            throw $e;
        } finally {
            $lock->release();
        }
    }
}
