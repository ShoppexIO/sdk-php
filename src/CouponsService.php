<?php

declare(strict_types=1);

namespace Shoppex;

final class CouponsService extends BaseService
{
    public function list(array $query = []): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/coupons/', params: $query, resourceClass: Coupon::class);
    }

    /** @return list<mixed> */
    public function listAll(array $query = []): array
    {
        return $this->client->collectCursor('/dev/v1/coupons/', $query, Coupon::class);
    }

    public function get(string $id): ApiResult
    {
        return $this->client->response('GET', "/dev/v1/coupons/{$id}", resourceClass: Coupon::class);
    }

    public function getByCode(string $code): ApiResult
    {
        return $this->client->response('GET', "/dev/v1/coupons/code/{$code}", resourceClass: Coupon::class);
    }

    public function create(array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', '/dev/v1/coupons/', json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Coupon::class);
    }

    public function update(string $id, array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('PATCH', "/dev/v1/coupons/{$id}", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Coupon::class);
    }

    public function delete(string $id, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('DELETE', "/dev/v1/coupons/{$id}", idempotencyKey: $idempotencyKey);
    }
}
