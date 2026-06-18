<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'vending_machine')]
class VendingMachineRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: 'json')]
    private array $slots = [];

    #[ORM\Column(type: 'json')]
    private array $changeInventory = [];

    #[ORM\Column(type: 'json')]
    private array $insertedCoins = [];

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function setSlots(array $slots): void
    {
        $this->slots = $slots;
    }

    public function setChangeInventory(array $change): void
    {
        $this->changeInventory = $change;
    }

    public function setInsertedCoins(array $coins): void
    {
        $this->insertedCoins = $coins;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function slots(): array
    {
        return $this->slots;
    }

    public function changeInventory(): array
    {
        return $this->changeInventory;
    }

    public function insertedCoins(): array
    {
        return $this->insertedCoins;
    }
}
