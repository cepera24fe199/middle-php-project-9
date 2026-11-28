<?php

namespace Hexlet\Code\Database;

use PDO;

class UrlRepository implements UrlRepositoryInterface
{
    public function __construct(private readonly PDO $database)
    {
    }

    public function insert(string $name, string $createdAt): ?int
    {
        $isPostgres = $this->database->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';
        $sql = 'INSERT INTO urls (name, created_at) VALUES (:name, :created_at)';
        $sql .= $isPostgres ? ' ON CONFLICT (name) DO NOTHING RETURNING id' : ' ON DUPLICATE KEY UPDATE id = id';
        $statement = $this->database->prepare($sql);

        $statement->execute(['name' => $name, 'created_at' => $createdAt]);

        if ($isPostgres) {
            $id = $statement->fetchColumn();
            return $id === false ? null : (int) $id;
        }

        return $statement->rowCount() === 1 ? (int) $this->database->lastInsertId() : null;
    }

    public function findIdByName(string $name): ?int
    {
        $statement = $this->database->prepare('SELECT id FROM urls WHERE name = :name');
        $statement->execute(['name' => $name]);
        $id = $statement->fetchColumn();

        return $id === false ? null : (int) $id;
    }

    public function findAll(): array
    {
        $urls = $this->database->query(
            'SELECT id, name, created_at FROM urls ORDER BY created_at DESC, id DESC'
        )->fetchAll();
        $latestChecks = $this->database->query(
            'SELECT url_id, created_at, status_code
             FROM (
                 SELECT url_id, created_at, status_code,
                     ROW_NUMBER() OVER (PARTITION BY url_id ORDER BY created_at DESC, id DESC) AS check_rank
                 FROM url_checks
             ) AS ranked_checks
             WHERE check_rank = 1'
        )->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

        return array_map(static function (array $url) use ($latestChecks): array {
            $check = $latestChecks[$url['id']] ?? [];

            return $url + [
                'last_check_at' => $check['created_at'] ?? null,
                'last_check_status_code' => $check['status_code'] ?? null,
            ];
        }, $urls);
    }

    public function findById(int $id): ?array
    {
        $statement = $this->database->prepare('SELECT id, name, created_at FROM urls WHERE id = :id');
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $url = $statement->fetch();

        return $url === false ? null : $url;
    }

    public function insertCheck(
        int $urlId,
        int $statusCode,
        string $createdAt,
        ?string $h1 = null,
        ?string $title = null,
        ?string $description = null,
    ): bool {
        $checkValues = $this->database->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql'
            ? 'CAST(:status_code AS INTEGER), CAST(:created_at AS TIMESTAMP)'
            : ':status_code, :created_at';
        $statement = $this->database->prepare(
            'INSERT INTO url_checks (url_id, status_code, created_at, h1, title, description)
             SELECT id, ' . $checkValues . ', :h1, :title, :description FROM urls WHERE id = :url_id'
        );
        $statement->execute([
            'url_id' => $urlId,
            'status_code' => $statusCode,
            'created_at' => $createdAt,
            'h1' => $h1,
            'title' => $title,
            'description' => $description,
        ]);

        return $statement->rowCount() === 1;
    }

    public function findChecksByUrlId(int $urlId): array
    {
        $statement = $this->database->prepare(
            'SELECT id, status_code, h1, title, description, created_at FROM url_checks
             WHERE url_id = :url_id ORDER BY created_at DESC, id DESC'
        );
        $statement->execute(['url_id' => $urlId]);

        return $statement->fetchAll();
    }
}
