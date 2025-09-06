<?php
require __DIR__ . '/config.php';

header('Content-Type: application/json');

// Razorpay sends JSON body and X-Razorpay-Signature header.
$raw       = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

http_response_code(200); // acknowledge quickly

try {
    if (!defined('WEBHOOK_SECRET') || WEBHOOK_SECRET === '') {
        throw new Exception('WEBHOOK_SECRET not set.');
    }
    \Razorpay\Api\Utility::verifyWebhookSignature($raw, $signature, WEBHOOK_SECRET);

    $event = json_decode($raw, true);
    // You can inspect $event['event'] and update your DB.
    file_put_contents(__DIR__ . '/webhook.log', date('c') . ' ' . ($event['event'] ?? 'unknown') . PHP_EOL, FILE_APPEND);

    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    file_put_contents(__DIR__ . '/webhook-error.log', date('c') . ' ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo json_encode(['ok' => false]);
}
