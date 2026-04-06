# shoppexio/shoppex-php

Official PHP SDK for the Shoppex Developer API.

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

## Status

This SDK is in early public MVP stage.
The package is a good first integration base, but the service surface still needs to grow.

## Docs

- Developer API docs: [docs.shoppex.io/api-reference/introduction](https://docs.shoppex.io/api-reference/introduction)
- SDK docs: [docs.shoppex.io/api-reference/sdks](https://docs.shoppex.io/api-reference/sdks)
