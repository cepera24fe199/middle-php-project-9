<?php

namespace Hexlet\Code\Tests\Feature;

use Dom\HTMLDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hexlet\Code\Tests\Support\DatabaseTestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;

class UrlsTest extends DatabaseTestCase
{
    private App $app;
    private array $session;

    protected function setUp(): void
    {
        $this->session = $_SESSION ?? [];
        $_SESSION = [];
        parent::setUp();

        $createApp = require __DIR__ . '/../../src/createApp.php';
        $handler = new MockHandler([
            new ConnectException('Connection failed', new Request('GET', 'https://feature.example')),
            new Response(203, [], '<title>Checked example</title>'),
        ]);
        $this->app = $createApp(
            fn () => $this->repository,
            new Client(['handler' => HandlerStack::create($handler)]),
        );
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->session;
        parent::tearDown();
    }

    public function testStoresUrlAndShowsOneTimeMessagesAfterRedirects(): void
    {
        $home = $this->request('GET', '/');
        self::assertSame(200, $home->getStatusCode());

        $created = $this->request('POST', '/urls', ['url' => 'https://feature.example/path?test=1']);
        self::assertSame(302, $created->getStatusCode());
        $location = $created->getHeaderLine('Location');
        self::assertMatchesRegularExpression('~^/urls/[1-9][0-9]*$~', $location);

        $details = $this->request('GET', $location);
        self::assertSame(200, $details->getStatusCode());
        self::assertStringContainsString('Страница успешно добавлена', (string) $details->getBody());
        self::assertStringNotContainsString(
            'Страница успешно добавлена',
            (string) $this->request('GET', $location)->getBody(),
        );

        $duplicate = $this->request('POST', '/urls', ['url' => 'https://FEATURE.example']);
        self::assertSame(302, $duplicate->getStatusCode());
        self::assertSame($location, $duplicate->getHeaderLine('Location'));
        self::assertStringContainsString(
            'Страница уже существует',
            (string) $this->request('GET', $location)->getBody(),
        );
    }

    public function testReturnsValidationAndMissingUrlErrors(): void
    {
        $invalid = $this->request('POST', '/urls', ['url' => 'invalid']);
        self::assertSame(422, $invalid->getStatusCode());
        self::assertStringContainsString('Некорректный URL', (string) $invalid->getBody());
        self::assertSame(404, $this->request('GET', '/urls/0')->getStatusCode());
        self::assertSame(404, $this->request('GET', '/urls/' . PHP_INT_MAX)->getStatusCode());
        self::assertSame(404, $this->request('POST', '/urls/' . PHP_INT_MAX . '/checks')->getStatusCode());
    }

    public function testShowsOnlyLatestCheckInList(): void
    {
        $id = $this->repository->insert('https://feature.example', '2000-01-01 12:00:00');
        $this->repository->insertCheck($id, 201, '2000-01-02 12:00:00');
        $this->repository->insertCheck($id, 202, '2000-01-03 12:00:00');
        $this->repository->insertCheck($id, 204, '2000-01-03 12:00:00');

        $response = $this->request('GET', '/urls');
        self::assertSame(200, $response->getStatusCode());
        $document = HTMLDocument::createFromString((string) $response->getBody(), LIBXML_NOERROR);
        $row = $document->querySelector("table[data-test=urls] tr:has(a[href='/urls/{$id}'])");
        self::assertNotNull($row);
        self::assertSame('https://feature.example', trim($row->querySelector('a')->textContent));
        self::assertSame('2000-01-03 12:00:00', trim($row->querySelector('td:nth-child(4)')->textContent));
        self::assertSame('204', trim($row->querySelector('td:nth-child(5)')->textContent));
    }

    public function testCheckRedirectsAndShowsFailureThenSuccess(): void
    {
        $id = $this->repository->insert('https://feature.example', '2000-01-01 12:00:00');
        $path = "/urls/{$id}";

        $failed = $this->request('POST', "{$path}/checks");
        self::assertSame(302, $failed->getStatusCode());
        self::assertSame($path, $failed->getHeaderLine('Location'));
        self::assertSame([], $this->repository->findChecksByUrlId($id));
        self::assertStringContainsString(
            'Произошла ошибка при проверке',
            (string) $this->request('GET', $path)->getBody(),
        );

        $success = $this->request('POST', "{$path}/checks");
        self::assertSame(302, $success->getStatusCode());
        self::assertSame($path, $success->getHeaderLine('Location'));
        $details = (string) $this->request('GET', $path)->getBody();
        self::assertStringContainsString('Страница успешно проверена', $details);
        self::assertStringContainsString('Checked example', $details);
    }

    private function request(string $method, string $path, array $data = []): ResponseInterface
    {
        $request = (new ServerRequestFactory())->createServerRequest($method, $path)->withParsedBody($data);

        return $this->app->handle($request);
    }
}
