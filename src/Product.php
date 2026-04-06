<?php

declare(strict_types=1);

namespace Shoppex;

final class Product extends Resource
{
    public function name(): ?string
    {
        $name = $this->raw['name'] ?? null;

        return is_string($name) ? $name : null;
    }
}
