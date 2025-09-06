<?php
// === Razorpay credentials & config ===
// TODO: replace with your real keys from Razorpay Dashboard.
define('RAZORPAY_KEY_ID',     'rzp_test_RDROwhMbTqO2iV');
define('RAZORPAY_KEY_SECRET', 'kYPO5BPKqoitRuvpUSSHjeiR');
define('CURRENCY', 'INR');

// Optional: only if you use webhooks (set the same value on Razorpay dashboard)
define('WEBHOOK_SECRET', 'set_a_random_long_string_here');

// === Database connection (MySQLi) ===
$host   = "localhost";
$user   = "root";          // change if different
$pass   = "";              // change if you set a password
$dbname = "aavirbhav";     // your database name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    die("DB Connection failed: " . $conn->connect_error);
}

// Composer autoload for Razorpay PHP SDK
//   composer require razorpay/razorpay:^2
require __DIR__ . '/vendor/autoload.php';
