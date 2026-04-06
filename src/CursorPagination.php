<?php

declare(strict_types=1);

namespace Shoppex;

final class CursorPagination
{
    public function __construct(
        public readonly bool $hasMore = false,
        public readonly ?string $nextCursor = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'has_more' => $this->hasMore,
            'next_cursor' => $this->nextCursor,
        ];
    }
}
