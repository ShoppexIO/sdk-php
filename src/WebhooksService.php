<?php

declare(strict_types=1);

namespace Shoppex;

final class WebhooksService extends BaseService
{
    public function list(array $query = []): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/webhooks', params: $query, resourceClass: Webhook::class);
    }

    /** @return list<mixed> */
    public function listAll(array $query = []): array
    {
        return $this->client->collectCursor('/dev/v1/webhooks', $query, Webhook::class);
    }

    public function get(string $id): ApiResult
    {
        return $this->client->response('GET', "/dev/v1/webhooks/{$id}", resourceClass: Webhook::class);
    }

    public function create(array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', '/dev/v1/webhooks', json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Webhook::class);
    }

    public function update(string $id, array $payload, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('PATCH', "/dev/v1/webhooks/{$id}", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Webhook::class);
    }

    public function delete(string $id, ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('DELETE', "/dev/v1/webhooks/{$id}", idempotencyKey: $idempotencyKey);
    }

    public function events(): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/webhooks/events');
    }

    public function logs(array $query = []): ApiResult
    {
        return $this->client->response('GET', '/dev/v1/webhooks/logs', params: $query);
    }

    /** @return list<mixed> */
    public function logsAll(array $query = []): array
    {
        return $this->client->collectPage('/dev/v1/webhooks/logs', $query);
    }

    public function test(string $id, array $payload = [], ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', "/dev/v1/webhooks/{$id}/test", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Webhook::class);
    }

    public function rotateSecret(string $id, array $payload = [], ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', "/dev/v1/webhooks/{$id}/rotate-secret", json: $payload, idempotencyKey: $idempotencyKey, resourceClass: Webhook::class);
    }

    public function retryLog(string $id, array $payload = [], ?string $idempotencyKey = null): ApiResult
    {
        return $this->client->response('POST', "/dev/v1/webhooks/logs/{$id}/retry", json: $payload, idempotencyKey: $idempotencyKey);
    }
}
