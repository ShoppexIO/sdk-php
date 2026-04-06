<?php

declare(strict_types=1);

namespace Shoppex;

final class Customer extends Resource
{
    public function email(): ?string
    {
        $email = $this->raw['email'] ?? null;

        return is_string($email) ? $email : null;
    }
}
