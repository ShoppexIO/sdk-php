<?php

declare(strict_types=1);

namespace Shoppex;

final class Invoice extends Resource
{
    public function status(): ?string
    {
        $status = $this->raw['status'] ?? null;

        return is_string($status) ? $status : null;
    }
}
