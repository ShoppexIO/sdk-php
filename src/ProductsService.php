<?php

declare(strict_types=1);

namespace Shoppex;

final class ProductsService extends BaseService
{
    public function list(array $query = []): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/products/', params: $query, resourceClass: Product::class);
    }

    /** @return list<mixed> */
    public function listAll(array $query = []): array
    {
        return $this->client->collectCursor('/dev/v1/products/', $query, Product::class);
    }

    public function get(string $id): ApiResult
    {
        return $this->client->response('GET', "/dev/v1/products/{$id}", resourceClass: Product::class);
    }

    public function create(array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', '/dev/v1/products/', json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Product::class);
    }

    public function update(string $id, array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('PATCH', "/dev/v1/products/{$id}", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Product::class);
    }

    public function delete(string $id, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('DELETE', "/dev/v1/products/{$id}", idempotencyKey: $idempotencyKey);
    }
}
