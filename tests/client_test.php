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
use Shoppex\CursorPagination;
use Shoppex\Coupon;
use Shoppex\Invoice;
use Shoppex\PagePagination;
use Shoppex\Payment;
use Shoppex\Product;
use Shoppex\Resource;
use Shoppex\ShoppexClient;
use Shoppex\Webhook;

function assert_true(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$calls = [];
$responses = [
    [
        'status' => 200,
        'body' => [
            'data' => [['id' => 'prod_0', 'name' => 'Starter']],
            'pagination' => ['next_cursor' => 'cursor_1', 'has_more' => true],
        ],
    ],
    [
        'status' => 200,
        'body' => [
            'data' => [['id' => 'prod_1'], ['id' => 'prod_2']],
            'pagination' => ['next_cursor' => 'cursor_2', 'has_more' => true],
        ],
    ],
    [
        'status' => 200,
        'body' => [
            'data' => [['id' => 'prod_3']],
            'pagination' => ['next_cursor' => null, 'has_more' => false],
        ],
    ],
    [
        'status' => 200,
        'body' => [
            'data' => [['id' => 'log_1']],
            'pagination' => ['page' => 1, 'limit' => 1, 'total' => 2, 'total_pages' => 2, 'has_more' => true],
        ],
    ],
    [
        'status' => 200,
        'body' => [
            'data' => [['id' => 'log_2']],
            'pagination' => ['page' => 2, 'limit' => 1, 'total' => 2, 'total_pages' => 2, 'has_more' => false],
        ],
    ],
    [
        'status' => 200,
        'body' => [
            'data' => [['id' => 'log_0']],
            'pagination' => ['page' => 1, 'limit' => 1, 'total' => 1, 'total_pages' => 1, 'has_more' => false],
        ],
    ],
    [
        'status' => 200,
        'body' => [
            'data' => ['id' => 'ord_1', 'status' => 'completed'],
        ],
    ],
    [
        'status' => 200,
        'body' => [
            'data' => [['uniqid' => 'pay_1', 'status' => 'pending']],
            'pagination' => ['next_cursor' => null, 'has_more' => false],
        ],
    ],
    [
        'status' => 200,
        'body' => [
            'data' => ['uniqid' => 'inv_1', 'status' => 'open'],
        ],
    ],
    [
        'status' => 200,
        'body' => [
            'data' => ['id' => 'coupon_1', 'code' => 'SPRING25'],
        ],
    ],
    [
        'status' => 200,
        'body' => [
            'data' => ['id' => 'wh_1', 'url' => 'https://example.com/webhook'],
        ],
    ],
    [
        'status' => 401,
        'headers' => ['x-request-id' => 'req_123'],
        'body' => [
            'error' => [
                'code' => 'UNAUTHORIZED',
                'message' => 'Missing or invalid API key.',
                'doc_url' => 'https://docs.shoppex.io/api/errors#UNAUTHORIZED',
                'details' => [['field' => 'authorization', 'message' => 'Missing bearer token']],
            ],
        ],
    ],
];

$transport = function (string $method, string $path, array $params, ?array $json, ?string $idempotencyKey) use (&$responses, &$calls): array {
    $calls[] = [
        'method' => $method,
        'path' => $path,
        'params' => $params,
        'json' => $json,
        'idempotencyKey' => $idempotencyKey,
    ];

    return array_shift($responses);
};

$client = new ShoppexClient(apiKey: 'shx_test', transport: $transport);

$typedProducts = $client->products()->list(['limit' => 1]);
assert_true($typedProducts->pagination instanceof CursorPagination, 'Expected cursor pagination model.');
assert_true($typedProducts->data[0] instanceof Product, 'Expected typed Product item.');
assert_true($typedProducts->data[0]->name() === 'Starter', 'Expected product name on typed resource.');

$products = $client->products()->listAll(['limit' => 2]);
assert_true(count($products) === 3, 'Expected three products from cursor collection.');
assert_true($products[0] instanceof Product, 'Expected typed products from collectCursor.');
assert_true($calls[2]['params']['cursor'] === 'cursor_2', 'Expected cursor on second products call.');

$logs = $client->webhooks()->logsAll(['page' => 1, 'limit' => 1]);
assert_true(count($logs) === 2, 'Expected two webhook logs from page collection.');
assert_true($logs[0] instanceof Resource, 'Expected typed resources from collectPage.');
assert_true($calls[4]['params']['page'] === 2, 'Expected page=2 on second logs call.');

$logResponse = $client->webhooks()->logs(['page' => 1, 'limit' => 1]);
assert_true($logResponse->pagination instanceof PagePagination, 'Expected page pagination model.');
assert_true($logResponse->pagination->page === 1, 'Expected page=1 in page pagination.');

$completedOrder = $client->orders()->complete('ord_1', ['notify_customer' => true], 'idem_123');
assert_true($completedOrder->item() instanceof Resource, 'Expected typed resource for mutation response.');
assert_true($completedOrder->item()->get('status') === 'completed', 'Expected completed status.');
assert_true($calls[6]['idempotencyKey'] === 'idem_123', 'Expected forwarded idempotency key.');

$payments = $client->payments()->list(['limit' => 1]);
assert_true($payments->data[0] instanceof Payment, 'Expected typed payment.');
assert_true($payments->data[0]->status() === 'pending', 'Expected payment status.');

$invoice = $client->invoices()->get('inv_1');
assert_true($invoice->item() instanceof Invoice, 'Expected typed invoice.');
assert_true($invoice->item()->status() === 'open', 'Expected invoice status.');

$coupon = $client->coupons()->get('coupon_1');
assert_true($coupon->item() instanceof Coupon, 'Expected typed coupon.');
assert_true($coupon->item()->code() === 'SPRING25', 'Expected coupon code.');

$webhook = $client->webhooks()->get('wh_1');
assert_true($webhook->item() instanceof Webhook, 'Expected typed webhook.');
assert_true($webhook->item()->url() === 'https://example.com/webhook', 'Expected webhook url.');

try {
    $client->me()->get();
    throw new RuntimeException('Expected ApiError was not thrown.');
} catch (ApiError $error) {
    assert_true($error->status === 401, 'Expected 401 status.');
    assert_true($error->apiCode === 'UNAUTHORIZED', 'Expected UNAUTHORIZED code.');
    assert_true($error->docUrl === 'https://docs.shoppex.io/api/errors#UNAUTHORIZED', 'Expected doc URL.');
    assert_true($error->requestId === 'req_123', 'Expected request id.');
}

echo "PHP SDK tests passed.\n";
