<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Mapper\Doctrine;

use App\VendingMachine\Application\VendingMachineRepositoryInterface;
use App\VendingMachine\Domain\VendingMachine;
use App\VendingMachine\Infrastructure\Persistence\Doctrine\Entity\VendingMachineRecord;
use App\VendingMachine\Infrastructure\Persistence\Doctrine\MachineNotFoundException;
use App\VendingMachine\Infrastructure\Persistence\Doctrine\Mapper\VendingMachineMapper;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineVendingMachineRepository implements VendingMachineRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private VendingMachineMapper $mapper,
    ) {
    }

    public function get(int $id): VendingMachine
    {
        $record = $this->em->find(VendingMachineRecord::class, $id);

        if (!$record) {
            throw new MachineNotFoundException();
        }

        return $this->mapper->toDomain($record);
    }

    public function save(VendingMachine $machine): void
    {
        $record = $this->em->find(VendingMachineRecord::class, $machine->id());

        $record ??= new VendingMachineRecord();

        $this->mapper->hydrateRecord($machine, $record);

        $record->setUpdatedAt(new \DateTimeImmutable());

        $this->em->persist($record);
        $this->em->flush();
    }
}
