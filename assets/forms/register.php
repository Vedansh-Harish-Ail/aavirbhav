<?php
include 'db.php'; // DB connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $clgname = $_POST['clgname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password check
    if ($password !== $confirm_password) {
        echo "<script>alert('❌ Passwords do not match'); window.location.href='form.html';</script>";
        exit();
    }

    // Check duplicate email
    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "<script>alert('❌ Email already registered'); window.location.href='form.html';</script>";
        exit();
    }
    $check->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $sql = $conn->prepare("INSERT INTO users (username, number, clgname, email, password) VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param("sssss", $username, $phone, $clgname, $email, $hashed_password);

    if ($sql->execute()) {
        echo "<script>alert('✅ Registration Done! Please login'); window.location.href='form.html';</script>";
    } else {
        echo "<script>alert('❌ Registration failed'); window.location.href='form.html';</script>";
    }

    $sql->close();
    $conn->close();
}
?>
