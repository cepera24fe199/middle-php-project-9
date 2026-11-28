<?php

namespace Hexlet\Code\Tests\Unit\Service;

use Hexlet\Code\Database\UrlRepositoryInterface;
use Hexlet\Code\Service\UrlService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UrlServiceTest extends TestCase
{
    #[DataProvider('urlsToNormalize')]
    public function testNormalizesUrlBeforeInsertingIt(string $input, string $name): void
    {
        $repository = $this->createMock(UrlRepositoryInterface::class);
        $repository->expects($this->once())->method('insert')->with($name)->willReturn(7);

        self::assertSame(['id' => 7, 'added' => true], (new UrlService($repository))->add($input, 'Missing'));
    }

    public static function urlsToNormalize(): array
    {
        return [
            'case and path' => ['HTTPS://EXAMPLE.COM/path?query=1', 'https://example.com'],
            'default HTTP port' => ['http://example.com:80/path', 'http://example.com'],
            'default HTTPS port' => ['https://example.com:443/path', 'https://example.com'],
            'custom port' => ['https://example.com:8443/path', 'https://example.com:8443'],
        ];
    }

    public function testThrowsWhenDuplicateCannotBeFound(): void
    {
        $repository = $this->createStub(UrlRepositoryInterface::class);
        $repository->method('insert')->willReturn(null);
        $repository->method('findIdByName')->willReturn(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Existing URL is missing');
        (new UrlService($repository))->add('https://example.com', 'Existing URL is missing');
    }
}
