<?php

namespace Hexlet\Code\Service;

use Carbon\CarbonImmutable;
use Dom\HTMLDocument;
use GuzzleHttp\ClientInterface;
use Hexlet\Code\Database\UrlRepositoryInterface;

class UrlCheckService
{
    public function __construct(
        private readonly UrlRepositoryInterface $repository,
        private readonly ClientInterface $client,
    ) {
    }

    public function check(int $id): bool
    {
        $url = $this->repository->findById($id);

        if ($url === null) {
            return false;
        }

        $page = $this->client->request('GET', $url['name']);
        $document = HTMLDocument::createFromString((string) $page->getBody(), LIBXML_NOERROR);
        $description = $document->querySelector('meta[name="description" i][content]');

        return $this->repository->insertCheck(
            $id,
            $page->getStatusCode(),
            CarbonImmutable::now()->toDateTimeString(),
            $document->querySelector('h1')?->textContent,
            $document->querySelector('title')?->textContent,
            $description?->getAttribute('content'),
        );
    }
}
