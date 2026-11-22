<?php

namespace Hexlet\Code\Request;

use Psr\Http\Message\ServerRequestInterface;

class StoreUrlRequest
{
    public const MAX_URL_LENGTH = 255;

    public readonly string $value;
    public readonly ?string $errorKey;

    public function __construct(ServerRequestInterface $request)
    {
        $body = (array) $request->getParsedBody();
        $input = $body['url'] ?? '';
        $this->value = is_string($input) ? trim($input) : '';
        $this->errorKey = is_string($input) ? $this->validate($this->value) : 'url_invalid';
    }

    private function validate(string $value): ?string
    {
        if ($value === '') {
            return 'url_required';
        }

        if (strlen($value) > self::MAX_URL_LENGTH) {
            return 'url_too_long';
        }

        $parts = filter_var($value, FILTER_VALIDATE_URL) === false ? false : parse_url($value);
        $scheme = strtolower($parts['scheme'] ?? '');

        if (!isset($parts['host']) || !in_array($scheme, ['http', 'https'], true)) {
            return 'url_invalid';
        }

        return null;
    }
}
