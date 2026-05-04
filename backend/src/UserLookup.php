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
}
