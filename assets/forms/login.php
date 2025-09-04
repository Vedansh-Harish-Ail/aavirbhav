<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'aavirbhav');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (isset($_POST['email'], $_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['userid'] = $id;
            echo '<script>alert("Login successful!"); window.location.href = "../team.php";</script>';
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
}
?>
