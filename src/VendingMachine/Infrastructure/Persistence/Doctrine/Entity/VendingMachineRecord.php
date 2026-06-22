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

    #[ORM\Column(type: 'string')]
    private string $state;

    /**
     * @var array<string, array{product: string, price: int}>
     */
    #[ORM\Column(type: 'json')]
    private array $configuration = [];

    /**
     * @var list<array{product: string, quantity: int}>
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

    #[ORM\Column(type: 'integer')]
    private int $retainedCash = 0;

    /**
     * @param array<string, array{product: string, price: int}> $configuration
     */
    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @param list<array{product: string, quantity: int}> $slots
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

    public function setRetainedCash(int $retainedCash): void
    {
        $this->retainedCash = $retainedCash;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return array<string, array{product: string, price: int}>
     */
    public function configuration(): array
    {
        return $this->configuration;
    }

    /**
     * @return list<array{product: string, quantity: int}>
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

    public function retainedCash(): int
    {
        return $this->retainedCash;
    }

    public function state(): string
    {
        return $this->state;
    }
}
