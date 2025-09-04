<?php
$pid = isset($_GET['payment_id']) ? htmlspecialchars($_GET['payment_id']) : 'unknown';
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Payment Success</title>
<style>body{font-family:system-ui;max-width:720px;margin:40px auto;padding:0 16px}.ok{color:#0a7a0a}</style>
</head>
<body>
  <h1 class="ok">Payment Successful 🎉</h1>
  <p>Payment ID: <strong><?php echo $pid; ?></strong></p>
  <p><a href="../index.php">Make another payment</a></p>
</body></html>
