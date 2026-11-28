<?php

namespace Hexlet\Code\Service;

use Carbon\CarbonImmutable;
use Hexlet\Code\Database\UrlRepositoryInterface;
use RuntimeException;

class UrlService
{
    public function __construct(private readonly UrlRepositoryInterface $repository)
    {
    }

    public function add(string $url, string $missingUrlMessage): array
    {
        $name = $this->normalize($url);
        $id = $this->repository->insert($name, CarbonImmutable::now()->toDateTimeString());

        if ($id !== null) {
            return ['id' => $id, 'added' => true];
        }

        $id = $this->repository->findIdByName($name);

        if ($id === null) {
            throw new RuntimeException($missingUrlMessage);
        }

        return ['id' => $id, 'added' => false];
    }

    private function normalize(string $url): string
    {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme']);
        $host = strtolower($parts['host']);
        $port = $parts['port'] ?? null;
        $defaultPort = $scheme === 'https' ? 443 : 80;
        $name = "{$scheme}://{$host}";

        if ($port !== null && $port !== $defaultPort) {
            $name .= ":{$port}";
        }

        return $name;
    }
}
