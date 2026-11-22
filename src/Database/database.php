<?php

namespace Hexlet\Code\Database;

use Hexlet\Code\Service\TechnicalMessages;
use PDO;
use RuntimeException;

function connectDatabase(): PDO
{
    $host = getEnvironmentValue('DB_HOST') ?? '127.0.0.1';
    $port = filter_var(getEnvironmentValue('DB_PORT') ?? '3306', FILTER_VALIDATE_INT);
    $dbName = getEnvironmentValue('DB_DATABASE');
    $username = getEnvironmentValue('DB_USERNAME');

    if ($dbName === null || $dbName === '' || $username === null || $username === '') {
        throw new RuntimeException((new TechnicalMessages())->forLocale()['database_credentials_missing']);
    }

    if ($host === '' || $port === false || strpbrk($host . $dbName, ";\x00") !== false) {
        throw new RuntimeException((new TechnicalMessages())->forLocale()['database_settings_invalid']);
    }

    return new PDO(
        "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4",
        $username,
        getEnvironmentValue('DB_PASSWORD') ?? '',
        [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    );
}
