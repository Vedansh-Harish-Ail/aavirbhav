<?php require __DIR__ . '/config.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Razorpay PHP Starter</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; max-width: 720px; margin: 40px auto; padding: 0 16px; }
    .card { border: 1px solid #eee; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    .row { display: flex; gap: 12px; align-items: center; }
    input, button { font-size: 16px; padding: 10px 12px; border-radius: 8px; border: 1px solid #ccc; }
    button { cursor: pointer; }
    .muted { color: #666; font-size: 14px; }
    .ok { color: #0a7a0a; }
    .err { color: #b00020; }
    pre { background: #f8f8f8; padding: 12px; border-radius: 8px; overflow: auto; }
  </style>
</head>
<body>
  <h1>Razorpay PHP Starter</h1>
  <p class="muted">Demo: create an order on server, open Razorpay Checkout, verify the signature, (optionally) capture the payment, and handle webhooks.</p>
  <div class="card">
    <div class="row">
      <label for="amount">Amount (₹)</label>
      <input id="amount" type="number" min="1" step="1" value="499">
      <button id="payBtn">Pay with Razorpay</button>
    </div>
    <p class="muted">This will create an order with currency INR. Amounts are multiplied by 100 (paise).</p>
    <div id="msg"></div>
  </div>

  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    const msg = document.getElementById('msg');
    const payBtn = document.getElementById('payBtn');

    async function createOrder(amountRupees) {
      const res = await fetch('create_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ amount_rupees: amountRupees })
      });
      if (!res.ok) throw new Error('Failed to create order');
      return await res.json();
    }

    function showMessage(text, cls='') {
      msg.className = cls; msg.textContent = text;
    }

    payBtn.addEventListener('click', async () => {
      msg.textContent = '';
      let amt = parseInt(document.getElementById('amount').value, 10);
      if (!amt || amt < 1) { showMessage('Enter a valid amount', 'err'); return; }

      try {
        const order = await createOrder(amt);
        const options = {
          key: "<?php echo htmlspecialchars(RAZORPAY_KEY_ID, ENT_QUOTES); ?>",
          amount: order.amount, // in paise
          currency: order.currency,
          name: "Demo Store",
          description: "Test Transaction",
          order_id: order.id,
          prefill: {
            name: "Hari Kiran",
            email: "hari@example.com",
            contact: "9999999999"
          },
          notes: {
            demo_order_note: "Order receipt " + order.receipt
          },
          theme: { color: "#3399cc" },
          handler: function (response){
            // Send IDs to server for signature verification
            fetch('verify.php', {
              method: 'POST',
              headers: {'Content-Type': 'application/json'},
              body: JSON.stringify({
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_order_id: response.razorpay_order_id,
                razorpay_signature: response.razorpay_signature
              })
            }).then(r => r.json()).then(data => {
              if (data.success) {
                window.location.href = 'public/success.php?payment_id=' + encodeURIComponent(response.razorpay_payment_id);
              } else {
                showMessage('Verification failed: ' + (data.error || 'Unknown error'), 'err');
              }
            }).catch(e => showMessage('Verification error: ' + e.message, 'err'));
          }
        };
        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (resp){
          showMessage('Payment failed: ' + (resp.error && resp.error.description ? resp.error.description : 'Unknown'), 'err');
        });
        rzp.open();
      } catch (e) {
        showMessage(e.message, 'err');
      }
    });
  </script>
</body>
</html>
