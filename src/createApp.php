<?php

use DI\Container;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Hexlet\Code\PageController;
use Hexlet\Code\Database\UrlRepository;
use Hexlet\Code\Database\UrlRepositoryInterface;
use Hexlet\Code\Service\TechnicalMessages;
use Hexlet\Code\Service\UrlCheckService;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;

use function DI\autowire;
use function DI\factory;
use function DI\value;
use function Hexlet\Code\Database\connectDatabase;

return function (?Closure $createRepository = null, ?ClientInterface $client = null): App {
    $container = new Container();
    $app = AppFactory::create(container: $container);
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();
    $errorMiddleware = $app->addErrorMiddleware(false, true, true);

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

    $errorMiddleware->setErrorHandler(
        HttpNotFoundException::class,
        function () use ($app, $renderer): ResponseInterface {
            return $renderer->render($app->getResponseFactory()->createResponse(404), 'not-found.php');
        },
    );

    $container->set(PhpRenderer::class, $renderer);
    $container->set(RouteParserInterface::class, $routeParser);
    $container->set(UrlRepositoryInterface::class, factory(
        $createRepository ?? static fn (): UrlRepositoryInterface => (new ReflectionClass(UrlRepository::class))
            ->newLazyProxy(static fn (): UrlRepository => new UrlRepository(connectDatabase())),
    ));
    $container->set(ClientInterface::class, factory(
        static fn (): ClientInterface => $client ?? new Client([
            'timeout' => 10.0,
            'allow_redirects' => [
                'max' => 5,
                'protocols' => ['http', 'https'],
            ],
        ]),
    ));
    $container->set(PageController::class, autowire()
        ->constructorParameter('translations', $translations)
        ->constructorParameter('technicalMessages', $technicalMessages)
        ->constructorParameter('createCheckService', value(
            static fn (): UrlCheckService => $container->get(UrlCheckService::class),
        )));

    $app->get('/', [PageController::class, 'home'])->setName('home');
    $app->post('/urls', [PageController::class, 'storeUrl'])->setName('urls.store');
    $app->get('/urls', [PageController::class, 'listUrls'])->setName('urls.index');
    $app->get('/urls/{id:[0-9]+}', [PageController::class, 'showUrl'])->setName('url.show');
    $app->post('/urls/{id:[0-9]+}/checks', [PageController::class, 'checkUrl'])->setName('url.checks.store');

    return $app;
};
