<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$locale = 'ru';
$translations = require __DIR__ . "/../lang/{$locale}.php";
$renderer = new PhpRenderer(__DIR__ . '/../templates', [
    'locale' => $locale,
    'translations' => $translations,
], 'layout.php');

$app->get('/', function (Request $request, Response $response) use ($renderer): Response {
    return $renderer->render($response, 'home.php');
});

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->run();
