<?php

declare(strict_types=1);

namespace Shoppex;

final class PagePagination
{
    public function __construct(
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
        public readonly ?int $total = null,
        public readonly ?int $totalPages = null,
        public readonly bool $hasMore = false,
    ) {
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
            'total' => $this->total,
            'total_pages' => $this->totalPages,
            'has_more' => $this->hasMore,
        ];
    }
}
