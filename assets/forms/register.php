<?php
include 'db.php'; // your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $clgname = trim($_POST['clgname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if ($password !== $confirm_password) {
        die("❌ Passwords do not match");
    }
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, number, clgname, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $phone, $clgname, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>
            document.body.innerHTML += `<div id='successAlert' style=\"position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#4caf50;color:#fff;padding:15px 25px;border-radius:8px;font-size:16px;z-index:9999;animation:fadeInDown 0.5s ease-out;\">
                ✅ Registration Done! Please login.
            </div>`;
            setTimeout(function(){
                window.location.href='form.html';
            }, 2000);
        </script>
        <style>
            @keyframes fadeInDown {
                from { opacity: 0; transform: translate(-50%, -20px); }
                to { opacity: 1; transform: translate(-50%, 0); }
            }
        </style>";
    } else {
        echo "<script>
            document.body.innerHTML += `<div id='errorAlert' style=\"position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#f44336;color:#fff;padding:15px 25px;border-radius:8px;font-size:16px;z-index:9999;animation:shake 0.3s ease-in-out 2;\">
                ❌ Registration failed! Please try again.
            </div>`;
        </script>
        <style>
            @keyframes shake {
                0% { transform: translate(-50%, 0); }
                25% { transform: translate(-50%, -5px); }
                50% { transform: translate(-50%, 5px); }
                75% { transform: translate(-50%, -5px); }
                100% { transform: translate(-50%, 0); }
            }
        </style>";
    }
    $stmt->close();
    $conn->close();
}
?>
