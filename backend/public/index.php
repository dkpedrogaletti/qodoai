<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Http/JsonResponse.php';
require_once dirname(__DIR__) . '/src/UserLookup.php';

use App\Http\JsonResponse;
use App\UserLookup;
use PDO;

function maskEmail(string $email): string
{
    $parts = explode('@', $email, 2);
    if (count($parts) !== 2) {
        return '***';
    }

    $local = $parts[0];
    $domain = $parts[1];
    $prefix = substr($local, 0, 2);

    return $prefix . '***@' . $domain;
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$dsn = getenv('DATABASE_DSN') ?: 'sqlite:' . dirname(__DIR__) . '/var/demo.sqlite';
$pdo = new PDO($dsn);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

if ($path === '/health') {
    (new JsonResponse(200, ['ok' => true]))->send();
    exit;
}

if (preg_match('#^/users/(\d+)$#', (string) $path, $m)) {
    $id = (int) $m[1];
    if ($id < 1) {
        (new JsonResponse(400, ['error' => 'invalid_id']))->send();
        exit;
    }

    $user = (new UserLookup($pdo))->findById($id);
    if ($user === null) {
        (new JsonResponse(404, ['error' => 'not_found']))->send();
        exit;
    }

    (new JsonResponse(200, ['user' => $user]))->send();
    exit;
}

if ($path === '/users') {
    $queryParams = [];
    parse_str((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $queryParams);
    $prefix = isset($queryParams['prefix']) ? trim((string) $queryParams['prefix']) : '';
    $limit = isset($queryParams['limit']) ? (int) $queryParams['limit'] : 5;
    $limit = max(1, min($limit, 20));
    $fields = isset($queryParams['fields']) ? (string) $queryParams['fields'] : 'public';

    if ($prefix === '' || strlen($prefix) < 2) {
        (new JsonResponse(400, ['error' => 'prefix_too_short']))->send();
        exit;
    }

    if (!in_array($fields, ['public', 'full'], true)) {
        (new JsonResponse(400, ['error' => 'invalid_fields']))->send();
        exit;
    }

    if ($fields === 'full') {
        $providedToken = $_SERVER['HTTP_X_DEMO_TOKEN'] ?? '';
        $expectedToken = getenv('DEMO_ADMIN_TOKEN') ?: '';
        if ($expectedToken === '' || !hash_equals($expectedToken, $providedToken)) {
            (new JsonResponse(403, ['error' => 'forbidden']))->send();
            exit;
        }
    }

    $users = (new UserLookup($pdo))->searchByNamePrefix($prefix, $limit + 1);
    $hasMore = count($users) > $limit;
    if ($hasMore) {
        $users = array_slice($users, 0, $limit);
    }

    if ($fields === 'public') {
        $users = array_map(
            static fn (array $user): array => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => maskEmail((string) $user['email']),
            ],
            $users
        );
    }

    (new JsonResponse(200, [
        'users' => $users,
        'has_more' => $hasMore,
        'visible_count' => count($users),
    ]))->send();
    exit;
}

(new JsonResponse(404, ['error' => 'not_found']))->send();
