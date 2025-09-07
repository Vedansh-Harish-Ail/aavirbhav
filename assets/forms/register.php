<?php
session_start();
include 'db.php'; // Your DB connection file

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
        echo "<script>alert('Passwords do not match'); window.history.back();</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert query
    $stmt = $conn->prepare("INSERT INTO users (username, number, clgname, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $phone, $clgname, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration Done, Login Now'); window.location.href='form.html';</script>";
    } else {
        echo "<script>alert('Error: Could not register'); window.history.back();</script>";
    }

    $stmt->close();
}
?>
