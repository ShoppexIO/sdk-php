<?php

declare(strict_types=1);

namespace Shoppex;

final class CustomersService extends BaseService
{
    public function list(array $query = []): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/customers', params: $query, resourceClass: Customer::class);
    }

    /** @return list<mixed> */
    public function listAll(array $query = []): array
    {
        return $this->client->collectCursor('/dev/v1/customers', $query, Customer::class);
    }

    public function get(string $id): ApiResult
    {
        return $this->client->response('GET', "/dev/v1/customers/{$id}", resourceClass: Customer::class);
    }

    public function create(array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', '/dev/v1/customers', json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Customer::class);
    }

    public function update(string $id, array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('PATCH', "/dev/v1/customers/{$id}", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Customer::class);
    }

    public function delete(string $id, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('DELETE', "/dev/v1/customers/{$id}", idempotencyKey: $idempotencyKey);
    }
}
