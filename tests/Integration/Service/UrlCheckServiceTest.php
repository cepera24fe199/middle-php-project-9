<?php

namespace Hexlet\Code\Tests\Integration\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Hexlet\Code\Service\UrlCheckService;
use Hexlet\Code\Tests\Support\DatabaseTestCase;

class UrlCheckServiceTest extends DatabaseTestCase
{
    public function testStoresCheckStatusAndOptionalPageMetadata(): void
    {
        $id = $this->repository->insert('https://metadata.example', '2026-01-01 12:00:00');
        $html = '<title>Example Title</title><meta name="DESCRIPTION" content="Summary"><h1>Heading</h1>';
        $handler = new MockHandler([
            new Response(201, [], $html),
            new Response(202, [], '<p>No metadata</p>'),
        ]);
        $service = new UrlCheckService($this->repository, new Client(['handler' => HandlerStack::create($handler)]));

        self::assertFalse($service->check(PHP_INT_MAX));
        self::assertTrue($service->check($id));
        self::assertSame('https://metadata.example', (string) $handler->getLastRequest()->getUri());
        self::assertTrue($service->check($id));

        $checks = $this->repository->findChecksByUrlId($id);
        self::assertCount(2, $checks);
        self::assertSame(202, $checks[0]['status_code']);
        self::assertNull($checks[0]['h1']);
        self::assertNull($checks[0]['title']);
        self::assertNull($checks[0]['description']);
        self::assertSame(201, $checks[1]['status_code']);
        self::assertSame('Heading', $checks[1]['h1']);
        self::assertSame('Example Title', $checks[1]['title']);
        self::assertSame('Summary', $checks[1]['description']);
    }
}
