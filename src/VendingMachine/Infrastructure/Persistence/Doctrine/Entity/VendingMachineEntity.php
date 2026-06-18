<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class VendingMachineEntity
{
    #[ORM\Version]
    #[ORM\Column(type: "integer")]
    private int $version;
}
