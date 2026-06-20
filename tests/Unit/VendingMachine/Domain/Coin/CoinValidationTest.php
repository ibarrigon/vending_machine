<?php

declare(strict_types=1);

namespace App\Tests\Unit\VendingMachine\Domain\Coin;

use App\VendingMachine\Domain\Coin\Coin;
use PHPUnit\Framework\TestCase;
use ValueError;

final class CoinValidationTest extends TestCase
{
    public function testInvalidCoinIsRejected(): void
    {
        $this->expectException(ValueError::class);

        Coin::from(13);
    }
}
