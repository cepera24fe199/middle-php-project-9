<?php

use Hexlet\Code\Service\TechnicalMessages;

use function Hexlet\Code\Database\connectDatabase;

require __DIR__ . '/../../vendor/autoload.php';

$database = connectDatabase();
$isPostgres = $database->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';
$schemaPath = $isPostgres ? __DIR__ . '/../../database.sql' : __DIR__ . '/database.sql';
$schema = file_get_contents($schemaPath);

if ($schema === false) {
    throw new RuntimeException((new TechnicalMessages())->forLocale()['database_schema_unreadable']);
}

if ($isPostgres) {
    $database->exec($schema);
    return;
}

foreach (explode(';', trim($schema, " \n\r\t;")) as $statement) {
    $database->exec($statement);
}

$columnType = $database->prepare(
    "SELECT DATA_TYPE FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column"
);

foreach (['urls', 'url_checks'] as $table) {
    $columnType->execute(['table' => $table, 'column' => 'created_at']);
    $type = $columnType->fetchColumn();
    $columnType->closeCursor();

    if ($type === 'date') {
        $database->exec("ALTER TABLE `{$table}` MODIFY COLUMN created_at DATETIME NOT NULL");
    }
}

foreach (['h1', 'title', 'description'] as $column) {
    $columnType->execute(['table' => 'url_checks', 'column' => $column]);
    $type = $columnType->fetchColumn();
    $columnType->closeCursor();

    if ($type === 'text') {
        $database->exec("ALTER TABLE url_checks MODIFY COLUMN `{$column}` LONGTEXT");
    }
}
