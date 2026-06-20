<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Product;

use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\Catalog\InvalidProductException;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Machine\State\InvalidMachineStateException;
use Symfony\Component\Lock\LockFactory;

final class RefillSlotUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockFactory,
    ) {
    }

    public function execute(RefillSlotCommand $command): void
    {
        $lock = $this->lockFactory->createLock('machine_'.$command->machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($command->machineId);
            if (!$machine->canBeRefilled()) {
                throw new InvalidMachineStateException();
            }

            $product = ProductType::tryFrom($command->product);
            if (null === $product) {
                throw new InvalidProductException();
            }

            $machine->refillSlot($product);

            $this->repository->save($machine);
        } catch (\Throwable $e) {
            // TODO: Implement diferents exceptions and if machine becomes unavailable, set state as out of order
            throw $e;
        } finally {
            $lock->release();
        }
    }
}
