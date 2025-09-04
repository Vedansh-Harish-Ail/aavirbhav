<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'aavirbhav');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $clgname = trim($_POST['clgname']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    if ($password !== $confirm) {
        echo "Passwords do not match!";
        exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $chk = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $chk->bind_param("s", $email);
    $chk->execute();
    $chk->store_result();
    if ($chk->num_rows > 0) {
        echo '<script>alert("Email already exists!"); window.location.href = "form.html";</script>';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email,clgname, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email,$clgname, $hash);
        $stmt->execute();
        echo '<script>alert("Registration successful!...Please login"); window.location.href = "form.html";</script>';
    }
}
?>
