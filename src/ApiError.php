<?php

declare(strict_types=1);

namespace Shoppex;

use RuntimeException;

final class ApiError extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly ?string $requestId = null,
        public readonly ?string $apiCode = null,
        public readonly ?string $docUrl = null,
        public readonly mixed $details = null,
        public readonly mixed $raw = null,
    ) {
        parent::__construct($message, $status);
    }
}
