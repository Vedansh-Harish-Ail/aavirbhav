<?php
require __DIR__ . '/config.php';

use Razorpay\Api\Api;

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $amountRupees = isset($input['amount_rupees']) ? intval($input['amount_rupees']) : 499;
    if ($amountRupees < 1) { throw new Exception('Invalid amount'); }

    $amountPaise = $amountRupees * 100; // Razorpay expects paise
    $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

    $order = $api->order->create([
        'amount' => $amountPaise,
        'currency' => CURRENCY,
        'receipt' => 'rcpt_' . time(),
        'payment_capture' => 1 // auto-capture
    ]);

    echo json_encode($order->toArray());
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
