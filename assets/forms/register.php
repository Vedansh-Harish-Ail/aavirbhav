<?php
include 'db.php'; // Your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $clgname = $_POST['clgname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password match check
    if ($password !== $confirm_password) {
        echo "<script>
            document.body.innerHTML = `
                <div style='position:fixed;top:20px;left:50%;transform:translateX(-50%);
                background:#f44336;color:#fff;padding:15px 25px;border-radius:8px;font-size:16px;
                animation:shake 0.3s ease-in-out 2;z-index:9999;'>
                    ❌ Passwords do not match
                </div>
                <style>
                    @keyframes shake {
                        0%, 100% { transform: translate(-50%, 0); }
                        25% { transform: translate(-50%, -5px); }
                        50% { transform: translate(-50%, 5px); }
                        75% { transform: translate(-50%, -5px); }
                    }
                </style>
            `;
            setTimeout(()=>{ window.location.href='form.html'; }, 2000);
        </script>";
        exit();
    }

    // Duplicate email check
    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "<script>
            document.body.innerHTML = `
                <div style='position:fixed;top:20px;left:50%;transform:translateX(-50%);
                background:#ff9800;color:#fff;padding:15px 25px;border-radius:8px;font-size:16px;
                animation:shake 0.3s ease-in-out 2;z-index:9999;'>
                    ⚠️ Email already registered
                </div>
                <style>
                    @keyframes shake {
                        0%, 100% { transform: translate(-50%, 0); }
                        25% { transform: translate(-50%, -5px); }
                        50% { transform: translate(-50%, 5px); }
                        75% { transform: translate(-50%, -5px); }
                    }
                </style>
            `;
            setTimeout(()=>{ window.location.href='form.html'; }, 2000);
        </script>";
        exit();
    }
    $check->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert query
    $sql = $conn->prepare("INSERT INTO users (username, phone, clgname, email, password) VALUES (?, ?, ?, ?, ?)");
    $sql->bind_param("sssss", $username, $phone, $clgname, $email, $hashed_password);

    if ($sql->execute()) {
        echo "<script>
            document.body.innerHTML = `
                <div style='position:fixed;top:20px;left:50%;transform:translateX(-50%);
                background:#4caf50;color:#fff;padding:15px 25px;border-radius:8px;font-size:16px;
                animation:fadeInDown 0.5s ease-out;z-index:9999;'>
                    ✅ Registration Done! Please login
                </div>
                <style>
                    @keyframes fadeInDown {
                        from { opacity: 0; transform: translate(-50%, -20px); }
                        to { opacity: 1; transform: translate(-50%, 0); }
                    }
                </style>
            `;
            setTimeout(()=>{ window.location.href='form.html'; }, 2000);
        </script>";
    } else {
        echo "<script>
            document.body.innerHTML = `
                <div style='position:fixed;top:20px;left:50%;transform:translateX(-50%);
                background:#f44336;color:#fff;padding:15px 25px;border-radius:8px;font-size:16px;
                animation:shake 0.3s ease-in-out 2;z-index:9999;'>
                    ❌ Registration failed
                </div>
                <style>
                    @keyframes shake {
                        0%, 100% { transform: translate(-50%, 0); }
                        25% { transform: translate(-50%, -5px); }
                        50% { transform: translate(-50%, 5px); }
                        75% { transform: translate(-50%, -5px); }
                    }
                </style>
            `;
            setTimeout(()=>{ window.location.href='form.html'; }, 2000);
        </script>";
    }

    $sql->close();
    $conn->close();
}
?>
