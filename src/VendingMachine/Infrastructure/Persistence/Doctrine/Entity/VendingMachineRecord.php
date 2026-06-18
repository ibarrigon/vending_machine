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

    /**
     * @var list<array{product: string, price: int, quantity: int}>
     */
    #[ORM\Column(type: 'json')]
    private array $slots = [];

    /**
     * @var array<int, int>
     */
    #[ORM\Column(type: 'json')]
    private array $changeInventory = [];

    /**
     * @var list<int>
     */
    #[ORM\Column(type: 'json')]
    private array $insertedCoins = [];

    /**
     * @param list<array{product: string, price: int, quantity: int}> $slots
     */
    public function setSlots(array $slots): void
    {
        $this->slots = $slots;
    }

    /**
     * @param array<int, int> $change
     */
    public function setChangeInventory(array $change): void
    {
        $this->changeInventory = $change;
    }

    /**
     * @param list<int> $coins
     */
    public function setInsertedCoins(array $coins): void
    {
        $this->insertedCoins = $coins;
    }

    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return list<array{product: string, price: int, quantity: int}>
     */
    public function slots(): array
    {
        return $this->slots;
    }

    /**
     * @return array<int, int>
     */
    public function changeInventory(): array
    {
        return $this->changeInventory;
    }

    /**
     * @return list<int>
     */
    public function insertedCoins(): array
    {
        return $this->insertedCoins;
    }
}
