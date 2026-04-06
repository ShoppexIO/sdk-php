<?php

declare(strict_types=1);

namespace Shoppex;

final class Webhook extends Resource
{
    public function url(): ?string
    {
        $url = $this->raw['url'] ?? null;

        return is_string($url) ? $url : null;
    }
}
