<?php
require __DIR__ . '/config.php';

// Razorpay sends JSON body and X-Razorpay-Signature header.
$raw = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

http_response_code(200); // Acknowledge receipt early

try {
    \Razorpay\Api\Utility::verifyWebhookSignature($raw, $signature, WEBHOOK_SECRET);

    // Parse event
    $event = json_decode($raw, true);
    // TODO: handle event types like payment.captured, order.paid, payment.failed, refund.processed etc.
    // For demo, we log all events.
    file_put_contents(__DIR__ . '/webhook.log', date('c') . ' ' . ($event['event'] ?? 'unknown') . ' ' . $raw . PHP_EOL, FILE_APPEND);
} catch (Throwable $e) {
    // Log signature failures to investigate secrets mismatch or body parsing issues.
    file_put_contents(__DIR__ . '/webhook-error.log', date('c') . ' ' . $e->getMessage() . ' BODY: ' . $raw . PHP_EOL, FILE_APPEND);
}
