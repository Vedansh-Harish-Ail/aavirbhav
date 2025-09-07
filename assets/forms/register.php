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
        echo "<script src='https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js'></script>
<script>
    const popup = document.createElement('div');
    popup.innerHTML = '✅ Registration Done! Please login';
    popup.style.position = 'fixed';
    popup.style.top = '20px';
    popup.style.left = '50%';
    popup.style.transform = 'translateX(-50%)';
    popup.style.background = '#4caf50';
    popup.style.color = '#fff';
    popup.style.padding = '15px 25px';
    popup.style.borderRadius = '8px';
    popup.style.fontSize = 'clamp(14px, 2vw, 18px)';
    popup.style.zIndex = '9999';
    popup.style.animation = 'fadeInDown 0.5s ease-out';
    popup.style.textAlign = 'center';
    popup.style.maxWidth = '90%';
    popup.style.boxSizing = 'border-box';
    document.body.appendChild(popup);

    confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });

    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes fadeInDown {
            from { opacity: 0; transform: translate(-50%, -20px); }
            to { opacity: 1; transform: translate(-50%, 0); }
        }
    `;
    document.head.appendChild(style);

    setTimeout(() => { window.location.href = 'form.html'; }, 2000);
</script>";
    } else {
        echo "<script>alert('❌ Registration failed'); window.location.href='form.html';</script>";
    }

    $sql->close();
    $conn->close();
}
?>
