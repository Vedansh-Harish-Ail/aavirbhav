<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT username, number, clgname, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // This must match SELECT field count
    $stmt->bind_result($username, $phone, $clgname, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['username'] = $username;
        $_SESSION['phone'] = $phone;
        $_SESSION['clgname'] = $clgname;

        echo "<script>alert('Login Successful'); window.location.href='../team.php';</script>";
        exit();
    } else {
        echo "<script>alert('Invalid Password');window.location.href='form.html';</script>";
    }
} else {
    echo "<script>alert('No User Found');window.location.href='form.html';</script>";
}

}
?>
