# shoppexio/shoppex-php

Official PHP SDK for the Shoppex Developer API.

This package is built for backend apps, admin tooling, and server-side integrations that talk to `/dev/v1/*`.

## Install

```bash
composer require shoppexio/shoppex-php
```

## Quick Start

```php
use Shoppex\ShoppexClient;

$client = new ShoppexClient(apiKey: 'shx_your_api_key');

$me = $client->me()->get();
$products = $client->products()->list(['page' => 1, 'limit' => 20]);
```

## Auth

Use one of these:

- `apiKey` for your own server-to-server integrations
- `accessToken` for OAuth app installs

## Included Services

- `me`
- `products`
- `orders`
- `customers`
- `payments`
- `invoices`
- `coupons`
- `webhooks`

This covers the main backend workflows:

- product reads and writes
- order creation, completion, fulfillment, and refunds
- customer reads and writes
- payment and invoice flows
- coupon checks and CRUD
- webhook management and delivery logs

## Docs

- Developer API docs: [docs.shoppex.io/api-reference/introduction](https://docs.shoppex.io/api-reference/introduction)
- SDK docs: [docs.shoppex.io/api-reference/sdks](https://docs.shoppex.io/api-reference/sdks)
