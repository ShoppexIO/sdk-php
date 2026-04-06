<?php

declare(strict_types=1);

namespace Shoppex;

final class ShoppexClient
{
    private string $baseUrl;
    private string $token;
    /** @var callable|null */
    private $transport;

    public function __construct(
        ?string $apiKey = null,
        ?string $accessToken = null,
        string $baseUrl = 'https://api.shoppex.io',
        ?callable $transport = null,
    ) {
        $token = $apiKey ?? $accessToken;
        if ($token === null || $token === '') {
            throw new \InvalidArgumentException('ShoppexClient requires either apiKey or accessToken.');
        }

        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = $token;
        $this->transport = $transport;
    }

    public function me(): MeService
    {
        return new MeService($this);
    }

    public function products(): ProductsService
    {
        return new ProductsService($this);
    }

    public function orders(): OrdersService
    {
        return new OrdersService($this);
    }

    public function customers(): CustomersService
    {
        return new CustomersService($this);
    }

    public function payments(): PaymentsService
    {
        return new PaymentsService($this);
    }

    public function invoices(): InvoicesService
    {
        return new InvoicesService($this);
    }

    public function coupons(): CouponsService
    {
        return new CouponsService($this);
    }

    public function webhooks(): WebhooksService
    {
        return new WebhooksService($this);
    }

    public function request(
        string $method,
        string $path,
        array $params = [],
        ?array $json = null,
        ?string $idempotencyKey = null,
    ): array {
        if ($this->transport !== null) {
            $transportResult = ($this->transport)($method, $path, $params, $json, $idempotencyKey);
            $status = isset($transportResult['status']) ? (int) $transportResult['status'] : 200;
            $headers = isset($transportResult['headers']) && is_array($transportResult['headers']) ? $transportResult['headers'] : [];
            $payload = $transportResult['body'] ?? null;

            if ($status >= 400) {
                $requestId = isset($headers['x-request-id']) && is_string($headers['x-request-id'])
                    ? $headers['x-request-id']
                    : null;

                $message = 'Shoppex API request failed.';
                $apiCode = null;
                $docUrl = null;
                $details = $payload;

                if (is_array($payload) && isset($payload['error']) && is_array($payload['error'])) {
                    $nested = $payload['error'];
                    $message = isset($nested['message']) && is_string($nested['message']) ? $nested['message'] : $message;
                    $apiCode = isset($nested['code']) && is_string($nested['code']) ? $nested['code'] : null;
                    $docUrl = isset($nested['doc_url']) && is_string($nested['doc_url']) ? $nested['doc_url'] : null;
                    $details = $nested['details'] ?? null;
                }

                throw new ApiError($message, $status, $requestId, $apiCode, $docUrl, $details, $payload);
            }

            return is_array($payload) ? $payload : ['data' => $payload];
        }

        $url = $this->baseUrl . $path;
        if ($params !== []) {
            $url .= '?' . http_build_query($params);
        }

        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Accept: application/json',
        ];

        if ($json !== null) {
            $headers[] = 'Content-Type: application/json';
        }

        if ($idempotencyKey !== null) {
            $headers[] = 'Idempotency-Key: ' . $idempotencyKey;
        }

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_HEADER, true);

        if ($json !== null) {
            curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($json, JSON_THROW_ON_ERROR));
        }

        $rawResponse = curl_exec($handle);
        if ($rawResponse === false) {
            $message = curl_error($handle);
            curl_close($handle);
            throw new ApiError($message !== '' ? $message : 'Shoppex API request failed.', 0);
        }

        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $headerSize = (int) curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $headerText = substr($rawResponse, 0, $headerSize);
        $bodyText = substr($rawResponse, $headerSize);
        curl_close($handle);

        $payload = json_decode($bodyText, true);
        $requestId = null;

        foreach (explode("\r\n", $headerText) as $headerLine) {
            if (str_starts_with(strtolower($headerLine), 'x-request-id:')) {
                $requestId = trim(substr($headerLine, strlen('x-request-id:')));
            }
        }

        if ($status >= 400) {
            $message = 'Shoppex API request failed.';
            $apiCode = null;
            $docUrl = null;
            $details = $payload;

            if (is_array($payload) && isset($payload['error']) && is_array($payload['error'])) {
                $nested = $payload['error'];
                $message = isset($nested['message']) && is_string($nested['message']) ? $nested['message'] : $message;
                $apiCode = isset($nested['code']) && is_string($nested['code']) ? $nested['code'] : null;
                $docUrl = isset($nested['doc_url']) && is_string($nested['doc_url']) ? $nested['doc_url'] : null;
                $details = $nested['details'] ?? null;
            } elseif (is_array($payload) && isset($payload['message']) && is_string($payload['message'])) {
                $message = $payload['message'];
            }

            throw new ApiError($message, $status, $requestId, $apiCode, $docUrl, $details, $payload);
        }

        return is_array($payload) ? $payload : ['data' => $payload];
    }

    public function response(
        string $method,
        string $path,
        array $params = [],
        ?array $json = null,
        ?string $idempotencyKey = null,
        string $resourceClass = Resource::class,
    ): ApiResult {
        return $this->mapResponse(
            $this->request($method, $path, $params, $json, $idempotencyKey),
            $resourceClass,
        );
    }

    public function collectCursor(string $path, array $query = [], string $resourceClass = Resource::class): array
    {
        $items = [];
        $cursor = isset($query['cursor']) ? (string) $query['cursor'] : null;

        while (true) {
            $params = $query;
            if ($cursor !== null && $cursor !== '') {
                $params['cursor'] = $cursor;
            }

            $response = $this->request('GET', $path, params: $params);
            $data = isset($response['data']) && is_array($response['data']) ? $response['data'] : [];
            foreach ($data as $item) {
                $items[] = $this->mapResource($item, $resourceClass);
            }

            $pagination = isset($response['pagination']) && is_array($response['pagination'])
                ? $response['pagination']
                : [];

            $hasMore = isset($pagination['has_more']) ? (bool) $pagination['has_more'] : false;
            $cursor = isset($pagination['next_cursor']) && is_string($pagination['next_cursor'])
                ? $pagination['next_cursor']
                : null;

            if (!$hasMore || $cursor === null || $cursor === '') {
                break;
            }
        }

        return $items;
    }

    public function collectPage(string $path, array $query = [], string $resourceClass = Resource::class): array
    {
        $items = [];
        $page = isset($query['page']) ? (int) $query['page'] : 1;

        while (true) {
            $params = $query;
            $params['page'] = $page;

            $response = $this->request('GET', $path, params: $params);
            $data = isset($response['data']) && is_array($response['data']) ? $response['data'] : [];
            foreach ($data as $item) {
                $items[] = $this->mapResource($item, $resourceClass);
            }

            $pagination = isset($response['pagination']) && is_array($response['pagination'])
                ? $response['pagination']
                : [];

            $hasMore = isset($pagination['has_more']) ? (bool) $pagination['has_more'] : false;
            if (!$hasMore) {
                break;
            }

            $page += 1;
        }

        return $items;
    }

    public function mapResponse(array $payload, string $resourceClass = Resource::class): ApiResult
    {
        $data = $payload['data'] ?? null;
        $pagination = null;

        if (is_array($data) && array_is_list($data)) {
            $data = array_map(fn (mixed $item): mixed => $this->mapResource($item, $resourceClass), $data);
        } else {
            $data = $this->mapResource($data, $resourceClass);
        }

        if (isset($payload['pagination']) && is_array($payload['pagination'])) {
            $paginationPayload = $payload['pagination'];

            if (array_key_exists('next_cursor', $paginationPayload)) {
                $nextCursor = $paginationPayload['next_cursor'] ?? null;
                $pagination = new CursorPagination(
                    hasMore: (bool) ($paginationPayload['has_more'] ?? false),
                    nextCursor: is_string($nextCursor) ? $nextCursor : null,
                );
            } else {
                $pagination = new PagePagination(
                    page: isset($paginationPayload['page']) && is_int($paginationPayload['page']) ? $paginationPayload['page'] : null,
                    limit: isset($paginationPayload['limit']) && is_int($paginationPayload['limit']) ? $paginationPayload['limit'] : null,
                    total: isset($paginationPayload['total']) && is_int($paginationPayload['total']) ? $paginationPayload['total'] : null,
                    totalPages: isset($paginationPayload['total_pages']) && is_int($paginationPayload['total_pages']) ? $paginationPayload['total_pages'] : null,
                    hasMore: (bool) ($paginationPayload['has_more'] ?? false),
                );
            }
        }

        return new ApiResult($data, $pagination, $payload);
    }

    public function mapResource(mixed $value, string $resourceClass = Resource::class): mixed
    {
        return is_array($value) ? new $resourceClass($value) : $value;
    }
}
