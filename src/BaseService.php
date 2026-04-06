<?php

declare(strict_types=1);

namespace Shoppex;

abstract class BaseService
{
    public function __construct(protected ShoppexClient $client)
    {
    }
}
