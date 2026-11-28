<?php

namespace Hexlet\Code;

use Closure;
use GuzzleHttp\Exception\GuzzleException;
use Hexlet\Code\Database\UrlRepositoryInterface;
use Hexlet\Code\Request\ShowUrlRequest;
use Hexlet\Code\Request\StoreUrlRequest;
use Hexlet\Code\Service\UrlCheckService;
use Hexlet\Code\Service\UrlService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\PhpRenderer;

class PageController
{
    /** @param Closure(): UrlCheckService $createCheckService */
    public function __construct(
        private readonly PhpRenderer $renderer,
        private readonly RouteParserInterface $routes,
        private readonly array $translations,
        private readonly array $technicalMessages,
        private readonly UrlRepositoryInterface $repository,
        private readonly UrlService $urlService,
        private readonly Closure $createCheckService,
    ) {
    }

    public function home(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->render($response, 'index.php');
    }

    public function storeUrl(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $urlRequest = new StoreUrlRequest($request);

        if ($urlRequest->errorKey !== null) {
            return $this->renderer->render($response->withStatus(422), 'index.php', [
                'value' => $urlRequest->value,
                'error' => $this->translations[$urlRequest->errorKey],
            ]);
        }

        $result = $this->urlService->add($urlRequest->value, $this->technicalMessages['existing_url_missing']);
        $flash = $result['added'] ? 'url_added' : 'url_exists';
        $_SESSION['flashMessages'][] = [
            'type' => $result['added'] ? 'success' : 'info',
            'text' => $this->translations[$flash],
        ];

        return $this->redirectToUrl($response, $result['id']);
    }

    public function listUrls(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->render($response, 'urls.php', ['urls' => $this->repository->findAll()]);
    }

    public function showUrl(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $urlRequest = new ShowUrlRequest($request);
        $url = $this->repository->findById($urlRequest->id);

        if ($url === null) {
            throw new HttpNotFoundException($request);
        }

        $flashMessages = $_SESSION['flashMessages'] ?? [];
        unset($_SESSION['flashMessages']);

        return $this->renderer->render($response, 'url.php', [
            'url' => $url,
            'checks' => $this->repository->findChecksByUrlId($urlRequest->id),
            'flashMessages' => $flashMessages,
        ]);
    }

    public function checkUrl(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $urlRequest = new ShowUrlRequest($request);
        $service = ($this->createCheckService)();

        try {
            $saved = $service->check($urlRequest->id);
        } catch (GuzzleException) {
            $_SESSION['flashMessages'][] = [
                'type' => 'error',
                'text' => $this->translations['check_failed'],
            ];

            return $this->redirectToUrl($response, $urlRequest->id);
        }

        if (!$saved) {
            throw new HttpNotFoundException($request);
        }

        $_SESSION['flashMessages'][] = [
            'type' => 'success',
            'text' => $this->translations['check_added'],
        ];

        return $this->redirectToUrl($response, $urlRequest->id);
    }

    private function redirectToUrl(ResponseInterface $response, int $id): ResponseInterface
    {
        return $response->withHeader('Location', $this->routes->urlFor('url.show', ['id' => $id]))->withStatus(302);
    }
}
