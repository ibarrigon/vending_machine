<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain\Coin;

use App\VendingMachine\Domain\Coin\Coin;
use App\VendingMachine\Domain\Coin\InvalidCoinException;
use PHPUnit\Framework\TestCase;

final class CoinValidationTest extends TestCase
{
    public function testInvalidCoinIsRejected(): void
    {
        $this->expectException(InvalidCoinException::class);

        Coin::from(13);
    }
}
