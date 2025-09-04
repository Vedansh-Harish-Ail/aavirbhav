<?php
// === Razorpay credentials & config ===
// TODO: replace with your actual keys from Razorpay Dashboard.
define('RAZORPAY_KEY_ID', 'rzp_test_RDROwhMbTqO2iV');
define('RAZORPAY_KEY_SECRET', 'kYPO5BPKqoitRuvpUSSHjeiR');
define('CURRENCY', 'INR');
// Webhook secret you set while creating a webhook in Razorpay Dashboard (NOT your API secret).

// === Database connection (MySQLi) ===
$host = "localhost";
$user = "root";       // change if different
$pass = "";           // change if you set a password
$dbname = "aavirbhav"; // your database name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}


// Basic helper: load Composer autoloader
require __DIR__ . '/vendor/autoload.php';
