<?php

declare(strict_types=1);

namespace Shoppex;

class Resource
{
    public function __construct(public readonly array $raw)
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->raw) ? $this->raw[$key] : $default;
    }

    public function id(): ?string
    {
        $id = $this->raw['id'] ?? null;

        return is_string($id) ? $id : null;
    }

    public function uniqid(): ?string
    {
        $uniqid = $this->raw['uniqid'] ?? null;

        return is_string($uniqid) ? $uniqid : null;
    }

    public function toArray(): array
    {
        return $this->raw;
    }
}
