<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Catalog;

final class InvalidProductException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Incorrect product');
    }
}
