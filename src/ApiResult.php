<?php

declare(strict_types=1);

namespace Shoppex;

final class ApiResult
{
    public function __construct(
        public readonly mixed $data,
        public readonly CursorPagination|PagePagination|null $pagination = null,
        public readonly array $raw = [],
    ) {
    }

    public function item(): mixed
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return $this->raw;
    }
}
