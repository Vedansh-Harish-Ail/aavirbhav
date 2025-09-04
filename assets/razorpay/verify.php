<?php
require __DIR__ . '/config.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $razorpayPaymentId = $input['razorpay_payment_id'] ?? null;
    $razorpayOrderId   = $input['razorpay_order_id'] ?? null;
    $razorpaySignature = $input['razorpay_signature'] ?? null;

    if (!$razorpayPaymentId || !$razorpayOrderId || !$razorpaySignature) {
        throw new Exception('Missing required fields');
    }

    $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

    // ✅ Verify signature
    $attributes = [
        'razorpay_order_id' => $razorpayOrderId,
        'razorpay_payment_id' => $razorpayPaymentId,
        'razorpay_signature' => $razorpaySignature
    ];
    $api->utility->verifyPaymentSignature($attributes);

    // ✅ Fetch payment details
    $payment = $api->payment->fetch($razorpayPaymentId);
    $status  = $payment->status;
    $amount  = $payment->amount / 100; // convert paise → rupees
    $email   = $payment->email ?? "unknown@example.com";

    // ✅ Save to DB
    $stmt = $conn->prepare("INSERT INTO payments (email, utr, amount, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $email, $razorpayPaymentId, $amount, $status);
    $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => true,
        'status' => $status,
        'payment_id' => $razorpayPaymentId
    ]);
} catch (SignatureVerificationError $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Signature verification failed']);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
