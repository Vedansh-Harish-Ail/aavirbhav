<?php
include 'db.php'; // your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['username'];
    $phone = $_POST['phone'];
    $clgname = $_POST['clgname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, phone, clgname, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $phone, $clgname, $email, $password);

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
