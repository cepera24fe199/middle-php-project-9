<?php

use Hexlet\Code\Service\TechnicalMessages;

function getEnvironmentValue(string $name): ?string
{
    $value = $_ENV[$name] ?? getenv($name);

    if ($value !== false) {
        return $value;
    }

    $envFile = __DIR__ . '/../.env';

    if (!is_file($envFile)) {
        return null;
    }

    $settings = parse_ini_file($envFile, false, INI_SCANNER_RAW);

    if ($settings === false) {
        throw new RuntimeException((new TechnicalMessages())->forLocale()['environment_unreadable']);
    }

    return $settings[$name] ?? null;
}
