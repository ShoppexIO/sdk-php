<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/ApiError.php';
require_once __DIR__ . '/../src/Resource.php';
require_once __DIR__ . '/../src/Product.php';
require_once __DIR__ . '/../src/Order.php';
require_once __DIR__ . '/../src/Customer.php';
require_once __DIR__ . '/../src/Payment.php';
require_once __DIR__ . '/../src/Invoice.php';
require_once __DIR__ . '/../src/Coupon.php';
require_once __DIR__ . '/../src/Webhook.php';
require_once __DIR__ . '/../src/CursorPagination.php';
require_once __DIR__ . '/../src/PagePagination.php';
require_once __DIR__ . '/../src/ApiResult.php';
require_once __DIR__ . '/../src/BaseService.php';
require_once __DIR__ . '/../src/MeService.php';
require_once __DIR__ . '/../src/ProductsService.php';
require_once __DIR__ . '/../src/OrdersService.php';
require_once __DIR__ . '/../src/CustomersService.php';
require_once __DIR__ . '/../src/PaymentsService.php';
require_once __DIR__ . '/../src/InvoicesService.php';
require_once __DIR__ . '/../src/CouponsService.php';
require_once __DIR__ . '/../src/WebhooksService.php';
require_once __DIR__ . '/../src/ShoppexClient.php';

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
