<?php

namespace Hexlet\Code\Database;

interface UrlRepositoryInterface
{
    public function insert(string $name, string $createdAt): ?int;

    public function findIdByName(string $name): ?int;

    public function findAll(): array;

    public function findById(int $id): ?array;

    public function insertCheck(
        int $urlId,
        int $statusCode,
        string $createdAt,
        ?string $h1 = null,
        ?string $title = null,
        ?string $description = null,
    ): bool;

    public function findChecksByUrlId(int $urlId): array;
}
