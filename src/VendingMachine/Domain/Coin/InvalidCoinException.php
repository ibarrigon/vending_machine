<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Coin;

final class InvalidCoinException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Invalid Coin');
    }
}
