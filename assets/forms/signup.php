<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "login_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        $message = "❌ Passwords do not match!";
    } else {
        // Check if email already exists
        $check = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($check);

        if ($result->num_rows > 0) {
            $message = "⚠️ Email already registered!";
        } else {
            $sql = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
            if ($conn->query($sql) === TRUE) {
                $message = "✅ Sign Up Successful! You can now log in.";
            } else {
                $message = "❌ Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up Page</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Background Video -->
<div class="video-background">
  <iframe src="https://www.youtube.com/embed/fsJVmreYIvI?autoplay=1&mute=1&loop=1&playlist=fsJVmreYIvI"
    frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
</div>

<!-- Sign Up Form -->
<form class="form" method="POST" action="">
    <h2 style="text-align:center; margin-bottom:15px;">Create Account</h2>

    <div class="flex-column">
      <label>Email</label>
    </div>
    <div class="inputForm">
      <input placeholder="Enter your Email" class="input" type="email" name="email" required>
    </div>
    
    <div class="flex-column">
      <label>Password</label>
    </div>
    <div class="inputForm">
      <input placeholder="Enter your Password" class="input" type="password" name="password" required>
    </div>

    <div class="flex-column">
      <label>Confirm Password</label>
    </div>
    <div class="inputForm">
      <input placeholder="Confirm Password" class="input" type="password" name="confirm_password" required>
    </div>

    <button class="button-submit" type="submit">Sign Up</button>
    
    <p class="p">Already have an account? <a href="index.php" class="span">Sign In</a></p>
</form>

<!-- Message Box -->
<?php if ($message != ""): ?>
  <script>
    alert("<?php echo $message; ?>");
  </script>
<?php endif; ?>

</body>
</html>
