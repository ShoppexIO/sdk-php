<?php

declare(strict_types=1);

namespace Shoppex;

final class MeService extends BaseService
{
    public function get(): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/me');
    }

    public function capabilities(): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/me/capabilities');
    }
}
