<?php

declare(strict_types=1);

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) {
    throw new RuntimeException('Composer autoload not found. Run composer install in sdks/php first.');
}

require_once $autoload;

use Shoppex\ApiError;
use Shoppex\ShoppexClient;

$client = new ShoppexClient(apiKey: 'shx_your_api_key');

$me = $client->me()->get();
echo 'Connected store: ' . ($me->item()?->get('store_name') ?? 'unknown') . PHP_EOL;

$products = $client->products()->list(['limit' => 20]);
foreach ($products->data as $product) {
    echo ($product->uniqid() ?? $product->id() ?? 'unknown') . ' ' . ($product->name() ?? 'Unnamed') . PHP_EOL;
}

try {
    $completed = $client->orders()->complete(
        'ord_123',
        ['notify_customer' => true],
        'complete-ord-123',
    );

    echo 'Order status: ' . ($completed->item()?->status() ?? 'unknown') . PHP_EOL;
} catch (ApiError $error) {
    echo $error->status . ' ' . ($error->apiCode ?? 'UNKNOWN') . PHP_EOL;
}

$payments = $client->payments()->list(['limit' => 5]);
foreach ($payments->data as $payment) {
    echo 'Payment: ' . ($payment->uniqid() ?? 'unknown') . ' ' . ($payment->status() ?? 'unknown') . PHP_EOL;
}
