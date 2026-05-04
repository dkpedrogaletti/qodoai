<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Http/JsonResponse.php';
require_once dirname(__DIR__) . '/src/UserLookup.php';

use App\Http\JsonResponse;
use App\UserLookup;
use PDO;

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

(new JsonResponse(404, ['error' => 'not_found']))->send();
