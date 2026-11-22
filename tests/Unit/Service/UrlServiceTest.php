<?php

namespace Hexlet\Code\Tests\Unit\Service;

use Hexlet\Code\Database\UrlRepository;
use Hexlet\Code\Service\UrlService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UrlServiceTest extends TestCase
{
    public function testNormalizesUrlBeforeInsertingIt(): void
    {
        $cases = [
            ['HTTPS://EXAMPLE.COM/path?query=1', 'https://example.com'],
            ['http://example.com:80/path', 'http://example.com'],
            ['https://example.com:443/path', 'https://example.com'],
            ['https://example.com:8443/path', 'https://example.com:8443'],
        ];

        foreach ($cases as [$input, $name]) {
            $repository = $this->createMock(UrlRepository::class);
            $repository->expects($this->once())->method('insert')->with($name)->willReturn(7);

            self::assertSame(['id' => 7, 'added' => true], (new UrlService($repository))->add($input, 'Missing'));
        }
    }

    public function testThrowsWhenDuplicateCannotBeFound(): void
    {
        $repository = $this->createStub(UrlRepository::class);
        $repository->method('insert')->willReturn(null);
        $repository->method('findIdByName')->willReturn(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Existing URL is missing');
        (new UrlService($repository))->add('https://example.com', 'Existing URL is missing');
    }
}
