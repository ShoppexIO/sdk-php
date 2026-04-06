<?php

declare(strict_types=1);

namespace Shoppex;

final class InvoicesService extends BaseService
{
    public function list(array $query = []): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/invoices', params: $query, resourceClass: Invoice::class);
    }

    /** @return list<mixed> */
    public function listAll(array $query = []): array
    {
        return $this->client->collectCursor('/dev/v1/invoices', $query, Invoice::class);
    }

    public function get(string $uniqid): ApiResult
    {
        return $this->client->response('GET', "/dev/v1/invoices/{$uniqid}", resourceClass: Invoice::class);
    }

    public function complete(string $uniqid, array $payload = [], ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', "/dev/v1/invoices/{$uniqid}/complete", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Invoice::class);
    }
}
