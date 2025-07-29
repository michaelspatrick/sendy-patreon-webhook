<?php
  // Replace with your Sendy API settings
  $sendy_url = 'https://sendy.domain.com/subscribe';        // URL to your Sendy installation
  $list_id = 'YOUR_LIST_ID';                                // Replace with your Sendy list ID
  $sendy_api_key = 'YOUR_API_KEY';                          // Your Sendy API key
  $webhookSecret = 'PATREON_WEBHOOK_SECRET';                // Use your webhook secret from Patreon

  // Get the raw POST data from Patreon
  $input = file_get_contents('php://input');

  // Verify the signature (this is a simplified version)
  $patreon_signature = $_SERVER['HTTP_X_PATREON_SIGNATURE'];

  if (hash_hmac('md5', $input, $webhookSecret) !== $patreon_signature) {
    http_response_code(400);
    die('Invalid signature');
  } else {
    // Decode the incoming JSON data from Patreon
    $patron_data = json_decode($input, true);

    if (isset($patron_data['included']) && isset($patron_data['data'])) {
      $data     = $patron_data['data'];
      $included = $patron_data['included'];

      if (isset($included[0]['attributes']['email'])) {
        $name  = $included[0]['attributes']['full_name'];
        $email = $included[0]['attributes']['email'];
      } elseif (isset($included[1]['attributes']['email'])) {
        $name  = $included[1]['attributes']['full_name'];
        $email = $included[1]['attributes']['email'];
      } elseif (isset($included[2]['attributes']['email'])) {
        $name  = $included[2]['attributes']['full_name'];
        $email = $included[2]['attributes']['email'];
      } elseif (isset($patron_data['data']['attributes'])) {
        $name  = $patron_data['data']['attributes']['full_name'];
        $email = $patron_data['data']['attributes']['email'];
      } else {
        http_response_code(400);
        die('Data not found');
      }
    }

    // Prepare data for Sendy
    $post_data = [
        'email' => $email,
        'name' => $name,
        'list' => $list_id,
        'api_key' => $sendy_api_key
    ];

    // Initialize cURL for sending the POST request to Sendy
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sendy_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request to Sendy
    $response = curl_exec($ch);

    // Check if the request was successful
    if ($response === false) {
      error_log('Error adding subscriber to Sendy: ' . curl_error($ch));
    }

    // Close the cURL session
    curl_close($ch);

    // Return an HTTP 200 OK response to acknowledge receipt
    http_response_code(200);
  }
?>
