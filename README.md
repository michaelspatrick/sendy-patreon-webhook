# Sendy + Patreon Webhook Integration

This PHP script listens to Patreon webhook events and automatically subscribes new patrons to your [Sendy](https://sendy.co) email list.

---

## 🚀 Features

- Validates incoming Patreon webhook signatures
- Extracts patron email and name from the payload
- Subscribes users to a specified Sendy list using the Sendy API
- Compatible with webhook events like `members:create`, `members:update`, etc.

---

## 🧩 Requirements

- A running PHP server (7.0+ recommended)
- A Sendy installation with an active list and API key
- A valid Patreon webhook set up in your creator account
- Access to the Patreon webhook secret

---

## ⚙️ Configuration

Edit the following section in `sendy-patreon-webhook.php`:

```php
$sendy_url = 'https://sendy.domain.com/subscribe'; // Your Sendy subscribe URL
$list_id = 'YOUR_LIST_ID';                         // Your Sendy list ID
$sendy_api_key = 'YOUR_API_KEY';                   // Your Sendy API key
$webhookSecret = 'PATREON_WEBHOOK_SECRET';         // Your Patreon webhook secret
```

---

## 📬 Setting Up the Webhook

1. Upload `sendy-patreon-webhook.php` to your server.
2. Make sure it's accessible via HTTPS (required by Patreon).
3. In your [Patreon creator dashboard](https://www.patreon.com/portal), go to **Developers > Webhooks**.
4. Add the public URL to this script (e.g., `https://yourdomain.com/sendy-patreon-webhook.php`)
5. Select the relevant event types such as:
   - `members:create`
   - `members:update`

---

## 🔐 Security

The script validates each incoming webhook using an HMAC MD5 signature based on your Patreon webhook secret:

```php
if (hash_hmac('md5', $input, $webhookSecret) !== $_SERVER['HTTP_X_PATREON_SIGNATURE']) {
    http_response_code(400);
    die('Invalid signature');
}
```

This helps prevent unauthorized requests from being processed.

---

## ✅ Sample Response Flow

- Patron pledges or updates their membership
- Patreon sends a webhook with patron info
- This script:
  - Verifies the signature
  - Extracts patron name and email
  - Submits them to Sendy using `curl`
  - Logs errors if Sendy fails to respond
  - Returns HTTP 200 OK

---

## 📝 License

MIT License

---

## 👨‍💻 Author

Developed by Dragon Society International (DSI)

