<?php

declare(strict_types=1);

namespace Shoppex;

final class Coupon extends Resource
{
    public function code(): ?string
    {
        $code = $this->raw['code'] ?? null;

        return is_string($code) ? $code : null;
    }
}
