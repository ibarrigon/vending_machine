<?php

declare(strict_types=1);

use App\Tests\Unit\VendingMachine\Domain\VendingMachineFactory;
use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\Catalog\ProductType;
use App\VendingMachine\Domain\Coin\Coin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class VendingMachinePersistenceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private VendingMachineRepositoryInterface $repo;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->repo = self::getContainer()->get(VendingMachineRepositoryInterface::class);
    }

    public function testSnapshotIsConsistentAfterRoundtrip(): void
    {
        $machine = VendingMachineFactory::create();

        $machine->insertCoin(Coin::ONE_EURO);
        $machine->selectProduct(ProductType::SODA);

        $this->repo->save($machine);

        $reloaded = $this->repo->get($machine->id());

        $this->assertSame(
            $machine->state(),
            $reloaded->state()
        );

        $this->assertSame(
            $machine->coinMachine()->insertedCoins()->total(),
            $reloaded->coinMachine()->insertedCoins()->total()
        );
    }
}
