<?php

declare(strict_types=1);

namespace App\Tests\Functional\Technician;

use App\VendingMachine\Application\Tecnician\Change\RefillChangeCommand;
use App\VendingMachine\Application\Tecnician\Change\RefillChangeUseCase;
use App\VendingMachine\Application\Tecnician\CloseMachineUseCase;
use App\VendingMachine\Application\Tecnician\OpenMachineUseCase;
use App\VendingMachine\Application\Tecnician\Product\RefillSlotCommand;
use App\VendingMachine\Application\Tecnician\Product\RefillSlotUseCase;
use App\VendingMachine\Application\Tecnician\ResetCreditUseCase;
use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class VendingMachineLifecycleTest extends KernelTestCase
{
    private VendingMachineRepositoryInterface $repository;
    private OpenMachineUseCase $open;
    private CloseMachineUseCase $close;
    private RefillSlotUseCase $refillSlot;
    private RefillChangeUseCase $refillChange;
    private ResetCreditUseCase $reset;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        /** @var VendingMachineRepositoryInterface $repository */
        $repository = $container->get(VendingMachineRepositoryInterface::class);
        $this->repository = $repository;

        /** @var OpenMachineUseCase $open */
        $open = $container->get(OpenMachineUseCase::class);
        $this->open = $open;

        /** @var CloseMachineUseCase $close */
        $close = $container->get(CloseMachineUseCase::class);
        $this->close = $close;

        /** @var RefillSlotUseCase $refillSlot */
        $refillSlot = $container->get(RefillSlotUseCase::class);
        $this->refillSlot = $refillSlot;

        /** @var RefillChangeUseCase $refillChange */
        $refillChange = $container->get(RefillChangeUseCase::class);
        $this->refillChange = $refillChange;

        /** @var ResetCreditUseCase $reset */
        $reset = $container->get(ResetCreditUseCase::class);
        $this->reset = $reset;
    }

    public function testTecnicianWork(): void
    {
        // Tecnician opens machine
        $this->open->execute(1);

        $machine = $this->repository->get(1);
        $this->assertTrue($machine->isOpen());
        $this->assertTrue($machine->canBeRefilled());
        try {
            $machine->slotByProduct(ProductType::WATER);
        } catch (\Exception $e) {
            $this->addToAssertionCount(1);
        }

        $this->refillSlot->execute(new RefillSlotCommand(1, ProductType::WATER->value));
        $machine = $this->repository->get(1);
        $this->assertNotEquals(0, $machine->slotByProduct(ProductType::WATER));

        $this->assertFalse($machine->coinMachine()->changeBox()->has(Coin::ONE_EURO));
        $this->refillChange->execute(new RefillChangeCommand(1, Coin::ONE_EURO->value, 10));

        $machine = $this->repository->get(1);
        $this->assertTrue($machine->coinMachine()->changeBox()->has(Coin::ONE_EURO));

        $this->reset->execute(1);
        $machine = $this->repository->get(1);
        $this->assertFalse($machine->coinMachine()->changeBox()->has(Coin::ONE_EURO));

        // tecnician close machine
        $this->close->execute(1);

        $machine = $this->repository->get(1);
        $this->assertTrue($machine->isReady());
    }
}
