<?php

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Hexlet\Code\PageController;
use Hexlet\Code\Database\UrlRepository;
use Hexlet\Code\Service\TechnicalMessages;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

use function Hexlet\Code\Database\connectDatabase;

return function (?Closure $createRepository = null, ?ClientInterface $client = null): App {
    $app = AppFactory::create();
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $app->addErrorMiddleware(false, true, true);

    $routeParser = $app->getRouteCollector()->getRouteParser();
    $messageCatalog = new TechnicalMessages();
    $locale = getEnvironmentValue('APP_LOCALE') ?? 'ru';

    if (!preg_match('/\A[a-zA-Z][a-zA-Z0-9_-]*\z/', $locale) || !is_file(__DIR__ . "/../lang/{$locale}.php")) {
        throw new RuntimeException($messageCatalog->forLocale()['unsupported_locale']);
    }

    $translations = require __DIR__ . "/../lang/{$locale}.php";
    $technicalMessages = $messageCatalog->forLocale($locale);
    $renderer = new PhpRenderer(__DIR__ . '/../templates', [
        'locale' => $locale,
        'translations' => $translations,
        'routes' => $routeParser,
    ], 'layout.php');

    $controller = new PageController(
        $renderer,
        $routeParser,
        $translations,
        $technicalMessages,
        $createRepository ?? fn (): UrlRepository => new UrlRepository(connectDatabase()),
        $client ?? new Client([
            'timeout' => 10.0,
            'allow_redirects' => [
                'max' => 5,
                'protocols' => ['http', 'https'],
            ],
        ]),
    );

    $app->get('/', [$controller, 'home'])->setName('home');
    $app->post('/urls', [$controller, 'storeUrl'])->setName('urls.store');
    $app->get('/urls', [$controller, 'listUrls'])->setName('urls.index');
    $app->get('/urls/{id:[0-9]+}', [$controller, 'showUrl'])->setName('url.show');
    $app->post('/urls/{id:[0-9]+}/checks', [$controller, 'checkUrl'])->setName('url.checks.store');

    return $app;
};
