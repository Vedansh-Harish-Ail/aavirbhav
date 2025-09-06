<?php
require __DIR__ . '/config.php';
session_start();

/*
  This page expects you have already set registration details in session, e.g.:

  $_SESSION['registration'] = [
      'amount'  => 100,              // rupees (integer)
      'name'    => 'John Doe',       // payer's name (optional)
      'contact' => '9876543210',     // phone (optional)
      'email'   => 'john@mail.com',  // email (optional)
      // any other fields you want to save after payment…
  ];

  Do NOT hardcode the amount here.
*/

$reg = $_SESSION['registration'] ?? [];
$amount = isset($reg['amount']) ? (int)$reg['amount'] : 0;
$userName = $reg['name']    ?? 'Guest User';
$userContact = $reg['contact'] ?? '';
$userEmail   = $reg['email']   ?? '';

if ($amount <= 0) {
    // No amount => send user back to your registration page
    header("Location: team.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Event Payment</title>
  <style>
    :root { --p: #2f7dd1; --p2:#215ca0; --ok:#207227; --err:#b00020; }
    body { font-family: system-ui, Arial, sans-serif; max-width: 760px; margin:40px auto; padding:0 16px; }
    .card { border: 1px solid #eee; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.05); }
    .row { display: flex; gap: 12px; align-items: center; }
    button { font-size: 16px; padding: 10px 14px; border-radius: 10px; cursor: pointer; background: var(--p); color: #fff; border: none; }
    button:disabled { opacity: .6; cursor: not-allowed; }
    .muted { color: #666; font-size: 14px; }
    #msg { margin-top:12px; }
    .ok { color: var(--ok); }
    .err { color: var(--err); }
  </style>
</head>
<body>
  <h1>Pay for Your Event Registration</h1>
  <div class="card">
    <div class="row">
      <button id="payBtn">Pay Now (₹<?php echo (int)$amount; ?>)</button>
    </div>
    <p class="muted">Your registration details will be saved after successful payment.</p>
    <div id="msg"></div>
  </div>

  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    const payBtn = document.getElementById('payBtn');
    const msg    = document.getElementById('msg');

    function showMessage(text, cls='') {
      msg.className = cls;
      msg.textContent = text;
    }

    payBtn.addEventListener('click', async () => {
      try {
        payBtn.disabled = true;
        showMessage('Creating order…');

        // 1) Create order on server (uses amount from SESSION)
        const res = await fetch('create_order.php', { method: 'POST' });
        const order = await res.json();

        if (!res.ok) {
          throw new Error(order.error || 'Failed to create order');
        }
        if (!order.id) {
          throw new Error('Server did not return a valid order id');
        }

        // 2) Open Razorpay Checkout
        const options = {
          key: "<?php echo RAZORPAY_KEY_ID; ?>",
          amount: order.amount,          // in paise
          currency: order.currency,
          name: "Event Payment",
          description: "Payment for Registered Events",
          order_id: order.id,
          prefill: {
            name: <?php echo json_encode($userName); ?>,
            contact: <?php echo json_encode($userContact); ?>,
            email: <?php echo json_encode($userEmail); ?>
          },
          handler: async function (response) {
            // 3) Verify signature on server
            try {
              const v = await fetch('verify.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(response)
              });
              const data = await v.json();
              if (!v.ok || !data.success) {
                throw new Error(data.error || 'Verification failed');
              }
              alert('Payment successful! Your registration is confirmed.');
              window.location.href = 'public/success.php';
            } catch (e) {
              showMessage('Verification failed: ' + e.message, 'err');
              payBtn.disabled = false;
            }
          },
          modal: { ondismiss: () => { payBtn.disabled = false; } }
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (resp) {
          showMessage('Payment failed: ' + (resp.error && resp.error.description ? resp.error.description : 'Unknown error'), 'err');
          payBtn.disabled = false;
        });
        rzp.open();
      } catch (e) {
        showMessage('Error: ' + e.message, 'err');
        payBtn.disabled = false;
      }
    });
  </script>
</body>
</html>
