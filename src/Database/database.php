<?php

namespace Hexlet\Code\Database;

use Hexlet\Code\Service\TechnicalMessages;
use PDO;
use RuntimeException;

function connectDatabase(): PDO
{
    $options = [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $databaseUrl = getEnvironmentValue('DATABASE_URL');
    if ($databaseUrl !== null && $databaseUrl !== '') {
        $url = parse_url($databaseUrl);
        $host = trim(rawurldecode($url['host'] ?? ''), '[]');
        $dbName = rawurldecode(substr($url['path'] ?? '', 1));
        $username = rawurldecode($url['user'] ?? '');

        if (
            !in_array($url['scheme'] ?? '', ['postgres', 'postgresql'], true)
            || $host === '' || $dbName === '' || $username === ''
            || strpbrk($host . $dbName, ";\x00") !== false
        ) {
            throw new RuntimeException((new TechnicalMessages())->forLocale()['database_url_invalid']);
        }

        $port = $url['port'] ?? 5432;
        parse_str($url['query'] ?? '', $parameters);
        $sslMode = $parameters['sslmode'] ?? 'prefer';
        if (!in_array($sslMode, ['disable', 'allow', 'prefer', 'require', 'verify-ca', 'verify-full'], true)) {
            throw new RuntimeException((new TechnicalMessages())->forLocale()['database_url_invalid']);
        }

        $dsnHost = addcslashes($host, "\\'");
        $dsnDatabase = addcslashes($dbName, "\\'");

        return new PDO(
            "pgsql:host='{$dsnHost}';port={$port};dbname='{$dsnDatabase}';sslmode={$sslMode}",
            $username,
            rawurldecode($url['pass'] ?? ''),
            $options,
        );
    }

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
        $options,
    );
}
