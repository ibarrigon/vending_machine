<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Tecnician\Change;

use App\VendingMachine\Application\VendingMachineRepositoryInterface;

final readonly class RefillChangeUseCase
{
    public function __construct(
        private VendingMachineRepositoryInterface $repository,
        private LockFactory $lockService,
    ) {}

    public function __invoke(RefillChangeCommand $command): RefillResult 
    {
        return $this->lockService->run(
            $command->machineId,
            function () use ($command) {

                $machine = $this->repository->get(
                    $command->machineId
                );

                $result = $machine->refillChange(
                    Coin::from($command->coin),
                    $command->quantity
                );

                $this->repository->save($machine);

                return $result;
            }
        );
    }
}
