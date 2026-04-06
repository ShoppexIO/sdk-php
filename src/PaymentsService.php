<?php

declare(strict_types=1);

namespace Shoppex;

final class PaymentsService extends BaseService
{
    public function list(array $query = []): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/payments', params: $query, resourceClass: Payment::class);
    }

    /** @return list<mixed> */
    public function listAll(array $query = []): array
    {
        return $this->client->collectCursor('/dev/v1/payments', $query, Payment::class);
    }

    public function get(string $uniqid): ApiResult
    {
        return $this->client->response('GET', "/dev/v1/payments/{$uniqid}", resourceClass: Payment::class);
    }

    public function create(array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', '/dev/v1/payments', json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Payment::class);
    }

    public function complete(string $uniqid, array $payload = [], ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', "/dev/v1/payments/{$uniqid}/complete", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Payment::class);
    }
}
