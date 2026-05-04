<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Minimal JSON helper for the demo API (no framework).
 */
final class JsonResponse
{
    public function __construct(
        private readonly int $status,
        private readonly array $payload,
    ) {
    }

    public function send(): void
    {
        http_response_code($this->status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->payload, JSON_THROW_ON_ERROR);
    }
}
