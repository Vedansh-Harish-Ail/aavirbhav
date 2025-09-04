# Razorpay PHP Starter

A minimal, working reference for integrating Razorpay Standard Checkout with PHP.

## What's inside
- `index.php` — demo page with an amount field and **Pay** button.
- `create_order.php` — server endpoint to create an Order via Razorpay PHP SDK.
- `verify.php` — verifies payment signature from Checkout and (optionally) captures the payment.
- `webhook.php` — verifies webhook signatures (`X-Razorpay-Signature`) and logs events.
- `config.php` — put your API keys and secrets here.
- `composer.json` — pulls Razorpay PHP SDK.
- `public/` — success and failed pages (very basic).

## Quick start
1. Install dependencies:
   ```bash
   composer install
   ```
2. Open `config.php` and set:
   - `RAZORPAY_KEY_ID`
   - `RAZORPAY_KEY_SECRET`
   - `WEBHOOK_SECRET` (create one while adding a webhook in Razorpay Dashboard)
3. Serve locally (e.g., PHP's built-in server):
   ```bash
   php -S localhost:8000 -t .
   ```
4. Visit http://localhost:8000 and try a **Test Mode** payment with Razorpay test keys.
5. Add a webhook in the Razorpay Dashboard pointing to `http://<your-host>/webhook.php` and use the same `WEBHOOK_SECRET`.

> Amounts are in paise. For ₹500.00, pass `50000`.
