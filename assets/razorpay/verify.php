<?php
session_start();
require '../forms/db.php';
require 'config.php';

if (!isset($_POST['razorpay_order_id'], $_POST['razorpay_payment_id'], $_POST['razorpay_signature'])) {
    die("Invalid payment request.");
}

$order_id    = $_POST['razorpay_order_id'];
$payment_id  = $_POST['razorpay_payment_id'];
$signature   = $_POST['razorpay_signature'];

// Verify Razorpay signature
$generated_signature = hash_hmac('sha256', $order_id . "|" . $payment_id, RAZORPAY_KEY_SECRET);

if ($generated_signature !== $signature) {
    die("Payment verification failed. Signature mismatch.");
}

// ===== Prepare variables for payment insert =====
$orderAmount   = $_SESSION['registration']['amount'];
$orderCurrency = "INR";
$userName      = $_SESSION['username'];
$userContact   = $_SESSION['phone'];
$userEmail     = $_SESSION['email'];

// Insert into payment table
$pay_sql = "INSERT INTO payments (order_id, payment_id, signature, amount, currency, name, contact, email, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($pay_sql);
$stmt->bind_param(
    "ssssssss",
    $order_id,
    $payment_id,
    $signature,
    $orderAmount,
    $orderCurrency,
    $userName,
    $userContact,
    $userEmail
);
$stmt->execute();
$stmt->close();

// ===== Prepare participants JSON =====
$participants = [];
foreach ($_SESSION['registration']['events'] as $event) {
    $names    = $_SESSION['registration']['names'][$event] ?? [];
    $contacts = $_SESSION['registration']['contacts'][$event] ?? [];
    $participants[$event] = [];
    for ($i = 0; $i < count($names); $i++) {
        $participants[$event][] = [
            'name'    => $names[$i] ?? '',
            'contact' => $contacts[$i] ?? ''
        ];
    }
}
$participants_json = json_encode($participants, JSON_UNESCAPED_UNICODE);

// ===== First extra participant for name1/contact1 =====
$firstExtraName    = '';
$firstExtraContact = '';
foreach ($participants as $people) {
    if (isset($people[1])) {
        $firstExtraName    = $people[1]['name'];
        $firstExtraContact = $people[1]['contact'];
        break;
    }
}

// ===== Prepare variables for registration insert =====
$regType      = $_SESSION['registration']['type'];
$events_str   = implode(",", $_SESSION['registration']['events']);
$regName      = $_SESSION['username'];
$regContact   = $_SESSION['phone'];
$regName1     = $firstExtraName;
$regContact1  = $firstExtraContact;
$regEmail     = $_SESSION['email'];
$regAmount    = $_SESSION['registration']['amount'];
$regPayID     = $payment_id;
$regOrderID   = $order_id;

// Insert into registration table
$reg_sql = "INSERT INTO registrations (type, event, name, contact, name1, contact1, participants_json, email, amount, payment_id, order_id, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($reg_sql);
$stmt->bind_param(
    "sssssssssss",
    $regType,
    $events_str,
    $regName,
    $regContact,
    $regName1,
    $regContact1,
    $participants_json,
    $regEmail,
    $regAmount,
    $regPayID,
    $regOrderID
);
$stmt->execute();
$stmt->close();

// Redirect to success page
$_SESSION['last_payment_id'] = $payment_id;
header("Location: public/success.php");
exit();
