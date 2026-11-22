<?php

namespace Hexlet\Code\Request;

use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;
use Psr\Http\Message\ServerRequestInterface;

class ShowUrlRequest
{
    public readonly int $id;

    public function __construct(ServerRequestInterface $request)
    {
        $route = RouteContext::fromRequest($request)->getRoute();
        $id = filter_var($route?->getArgument('id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($id === false) {
            throw new HttpNotFoundException($request);
        }

        $this->id = $id;
    }
}
