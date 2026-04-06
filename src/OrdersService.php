<?php

declare(strict_types=1);

namespace Shoppex;

final class OrdersService extends BaseService
{
    public function list(array $query = []): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/orders', params: $query, resourceClass: Order::class);
    }

    /** @return list<mixed> */
    public function listAll(array $query = []): array
    {
        return $this->client->collectCursor('/dev/v1/orders', $query, Order::class);
    }

    public function get(string $id): ApiResult
    {
        return $this->client->response('GET', "/dev/v1/orders/{$id}", resourceClass: Order::class);
    }

    public function create(array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', '/dev/v1/orders', json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Order::class);
    }

    public function fulfill(string $id, array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', "/dev/v1/orders/{$id}/fulfill", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Order::class);
    }

    public function complete(string $id, array $payload = [], ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', "/dev/v1/orders/{$id}/complete", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Order::class);
    }

    public function refund(string $id, array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', "/dev/v1/orders/{$id}/refund", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Order::class);
    }
}
