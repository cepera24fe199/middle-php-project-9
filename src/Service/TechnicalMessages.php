<?php

namespace Hexlet\Code\Service;

class TechnicalMessages
{
    public function forLocale(string $locale = 'ru'): array
    {
        $default = require __DIR__ . '/../../lang/technical/ru.php';
        $path = __DIR__ . "/../../lang/technical/{$locale}.php";

        if ($locale === 'ru' || !is_file($path)) {
            return $default;
        }

        return (require $path) + $default;
    }
}
