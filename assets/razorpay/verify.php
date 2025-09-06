<?php
session_start();
require __DIR__ . '/config.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

header('Content-Type: application/json');

try {
    // 1) Read Razorpay response from JS handler
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $razorpayPaymentId = $input['razorpay_payment_id'] ?? null;
    $razorpayOrderId   = $input['razorpay_order_id']   ?? null;
    $razorpaySignature = $input['razorpay_signature']  ?? null;

    if (!$razorpayPaymentId || !$razorpayOrderId || !$razorpaySignature) {
        throw new Exception('Missing required fields from Razorpay.');
    }

    // 2) Verify signature (non-static method in your Razorpay PHP SDK)
    $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
    $attributes = [
        'razorpay_order_id'   => $razorpayOrderId,
        'razorpay_payment_id' => $razorpayPaymentId,
        'razorpay_signature'  => $razorpaySignature
    ];
    $api->utility->verifyPaymentSignature($attributes); // throws on failure

    // 3) Save payment + registration to DB
    $amountRupees = isset($_SESSION['registration']['amount']) ? (int)$_SESSION['registration']['amount'] : 0;
    $name    = $_SESSION['registration']['name']    ?? '';
    $contact = $_SESSION['registration']['contact'] ?? '';
    $email   = $_SESSION['registration']['email']   ?? '';

    $stmt = $conn->prepare("INSERT INTO payments (order_id, payment_id, signature, amount, currency, name, contact, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $currency = CURRENCY;
    $stmt->bind_param("sssissss", $razorpayOrderId, $razorpayPaymentId, $razorpaySignature, $amountRupees, $currency, $name, $contact, $email);
    $stmt->execute();
    $paymentRowId = $stmt->insert_id;
    $stmt->close();

    // Optional: Save event-specific registrations
    if (!empty($_SESSION['registration']['events']) && is_array($_SESSION['registration']['events'])) {
        $type = $_SESSION['registration']['type'] ?? 'single';
        foreach ($_SESSION['registration']['events'] as $ev) {
            $stmt = $conn->prepare("INSERT INTO registrations (type, event, name, contact, email, amount, payment_id, order_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi ss", $type, $ev, $name, $contact, $email, $amountRupees, $razorpayPaymentId, $razorpayOrderId);
            $stmt->execute();
            $stmt->close();
        }
    }

    // 4) Clear registration session after success
    unset($_SESSION['registration']);

    echo json_encode(['success' => true, 'id' => $paymentRowId]);
} catch (SignatureVerificationError $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Signature verification failed: '.$e->getMessage()]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
