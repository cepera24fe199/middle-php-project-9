<?php

namespace Hexlet\Code\Tests\Unit\Request;

use Hexlet\Code\Request\StoreUrlRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;

class StoreUrlRequestTest extends TestCase
{
    #[DataProvider('submittedUrls')]
    public function testValidatesAndTrimsSubmittedUrl(string|array $input, string $value, ?string $errorKey): void
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('POST', '/urls')->withParsedBody(['url' => $input]);
        $result = new StoreUrlRequest($request);

        self::assertSame($value, $result->value);
        self::assertSame($errorKey, $result->errorKey);
    }

    public static function submittedUrls(): array
    {
        $maxLengthUrl = 'https://example.com/' . str_repeat('a', 235);

        return [
            'empty' => ['   ', '', 'url_required'],
            'array' => [['https://example.com'], '', 'url_invalid'],
            'no host' => ['https:///path', 'https:///path', 'url_invalid'],
            'unsupported scheme' => ['ftp://example.com', 'ftp://example.com', 'url_invalid'],
            'too long' => [$maxLengthUrl . 'a', $maxLengthUrl . 'a', 'url_too_long'],
            'at limit' => [$maxLengthUrl, $maxLengthUrl, null],
            'trimmed' => [' https://example.com ', 'https://example.com', null],
        ];
    }
}
