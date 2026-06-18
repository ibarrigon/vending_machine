<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\SelectProduct;

use App\VendingMachine\Application\TransactionResultDTO;
use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\Catalog\InvalidProductException;
use App\VendingMachine\Domain\Catalog\ProductType;
use Symfony\Component\Lock\LockFactory;

final class SelectProductUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockFactory,
    ) {}

    public function execute(SelectProductCommand $command): TransactionResultDTO
    {
        $lock = $this->lockFactory->createLock('machine_' . $command->machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($command->machineId);
            $product = ProductType::fromSelector($command->product);
            if ($product === null) {
                throw new InvalidProductException();
            }

            $result = $machine->selectProduct($product);

            $this->repository->save($machine);

            return new TransactionResultDTO(
                product: $result->product->value,
                change: $result->change,
            );
        } finally {
            $lock->release();
        }
    }
}
