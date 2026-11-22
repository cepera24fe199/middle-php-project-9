<?php

namespace Hexlet\Code\Tests\Support;

use Hexlet\Code\Database\UrlRepository;
use PDO;
use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    protected UrlRepository $repository;
    private PDO $database;

    protected function setUp(): void
    {
        $dsn = getenv('TEST_DB_DSN');
        if ($dsn === false || $dsn === '') {
            $this->markTestSkipped('Set TEST_DB_DSN to run the MySQL tests.');
        }

        $this->database = new PDO($dsn, getenv('TEST_DB_USER') ?: '', getenv('TEST_DB_PASSWORD') ?: '', [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $schema = $this->database->query('SELECT DATABASE()')->fetchColumn();
        self::assertIsString($schema);
        self::assertStringEndsWith('_test', $schema);

        $sql = file_get_contents(__DIR__ . '/../../src/Database/database.sql');
        foreach (explode(';', trim($sql, " \n\r\t;")) as $statement) {
            $this->database->exec($statement);
        }

        $this->database->beginTransaction();
        $this->repository = new UrlRepository($this->database);
    }

    protected function tearDown(): void
    {
        if (isset($this->database) && $this->database->inTransaction()) {
            $this->database->rollBack();
        }
    }
}
