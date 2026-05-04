<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Example data access with parameterized queries — a pattern PR-Agent should recognize as healthy.
 */
final class UserLookup
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, email, name FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    /**
     * Return up to $limit users whose name starts with $prefix.
     *
     * @return list<array{id:int,email:string,name:string}>
     */
    public function searchByNamePrefix(string $prefix, int $limit = 5): array
    {
        $safeLimit = max(1, min($limit, 20));
        $stmt = $this->pdo->prepare(
            'SELECT id, email, name FROM users WHERE name LIKE :prefix ORDER BY name ASC LIMIT :limit'
        );
        $stmt->bindValue(':prefix', $prefix . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $safeLimit, PDO::PARAM_INT);
        $stmt->execute();

        /** @var list<array{id:int,email:string,name:string}> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
