<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Machine\CashFlow;

final class InsufficientChangeException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No change');
    }
}
