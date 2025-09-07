<?php
session_start();
include 'db.php'; // Your DB connection file

// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $clgname = trim($_POST['clgname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate password match
    if ($password !== $confirm_password) {
        die("❌ Passwords do not match");
    }

    // Check if DB connection is OK
    if (!isset($conn) || $conn->connect_error) {
        die("❌ Database connection failed: " . ($conn->connect_error ?? 'Connection variable not set'));
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO users (username, number, clgname, email, password) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("❌ SQL Prepare failed: " . $conn->error);
    }

    // Bind params
    if (!$stmt->bind_param("sssss", $username, $phone, $clgname, $email, $hashed_password)) {
        die("❌ Bind failed: " . $stmt->error);
    }

    // Execute query
    if ($stmt->execute()) {
        echo "✅ Registration successful!";
    } else {
        die("❌ Execute failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    die("❌ Invalid request method");
}
