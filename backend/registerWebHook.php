<?php
// Yoco Secret API Key
$secret_key = getenv('YOCO_SECRET_KEY'); // Ensure this is set in your environment

// Webhook URL (replace with your actual webhook endpoint)
$webhook_url = "https://your-domain.com/webhook-handler.php"; 

// Webhook request data
$request_data = [
    "name" => "payment-webhook",
    "url" => $webhook_url
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://payments.yoco.com/api/webhooks',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $secret_key
    ],
    CURLOPT_POSTFIELDS => json_encode($request_data),
    CURLOPT_FOLLOWLOCATION => true
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Handle response
if ($http_code === 201) {
    echo "Webhook registered successfully: " . $response;
} else {
    echo "Failed to register webhook. Response: " . $response;
}
