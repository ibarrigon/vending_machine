<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Client\SelectProduct;

use App\VendingMachine\Application\Client\Command\SelectProductCommand;
use App\VendingMachine\Application\TransactionResultDTO;
use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\Catalog\InvalidProductException;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use Symfony\Component\Lock\LockFactory;

final readonly class SelectProductUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockFactory,
    ) {
    }

    public function execute(SelectProductCommand $command): TransactionResultDTO
    {
        $lock = $this->lockFactory->createLock('machine_'.$command->machineId);
        $lock->acquire(true);

        try {
            $machine = $this->repository->get($command->machineId);
            $product = ProductType::fromSelector($command->selector);
            if (null === $product) {
                throw new InvalidProductException();
            }

            $result = $machine->selectProduct($product);

            $this->repository->save($machine);

            return new TransactionResultDTO(
                product: $result->product->value,
                change: array_values(array_map(fn (Coin $coin): int => $coin->value, $result->change)),
                retainedCash: $result->retainedCash,
            );
        } catch (\Throwable $e) {
            // TODO: Implement diferents exceptions and if machine becomes unavailable, set state as out of order
            throw $e;
        } finally {
            $lock->release();
        }
    }
}
