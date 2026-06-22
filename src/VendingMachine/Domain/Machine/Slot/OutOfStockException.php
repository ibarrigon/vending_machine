<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\Slot;

final class OutOfStockException extends \Exception
{
    public function __construct()
    {
        parent::__construct('There is no stock for this product');
    }
}
