<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    private static ?\PDO $pdo = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->truncateDatabase();
    }

    protected function pdo(): \PDO
    {
        if (self::$pdo instanceof \PDO) {
            return self::$pdo;
        }

        $dsn = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');

        if (!is_string($dsn) || '' === $dsn) {
            throw new \RuntimeException('DATABASE_URL not set or invalid');
        }

        self::$pdo = new \PDO($dsn);
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return self::$pdo;
    }

    private function truncateDatabase(): void
    {
        $pdo = $this->pdo();

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        $tables = $this->getTables();

        foreach ($tables as $table) {
            $pdo->exec(sprintf('TRUNCATE TABLE `%s`', $table));
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * @return list<string>
     */
    private function getTables(): array
    {
        return [
            'vending_machine',
        ];
    }
}
