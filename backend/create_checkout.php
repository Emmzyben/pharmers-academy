<?php
session_start();
error_reporting(E_ALL);  // Show all errors
ini_set('display_errors', 1);  // Display errors

require_once '../backend/database/db_config.php';
// Input validation
$total_price = filter_input(INPUT_GET, 'total_price', FILTER_VALIDATE_FLOAT);
$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
$email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);

if (!$total_price || !$user_id || !$email) {
    $_SESSION['message'] = "Invalid checkout details.";
    $_SESSION['messageType'] = "error";
    header("Location: register.php");
    exit();
}

// Convert amount to cents
$amount_cents = intval($total_price * 100);

// Get secret key
$secret_key ="sk_test_5a9c72cbY6EyKZ0dcbe4f39adfc1"; // Ensure this is correctly set
if (!$secret_key) {
    error_log("Error: Missing Yoco Secret Key.");
    die("Error: Missing Yoco Secret Key.");
}

// Yoco Checkout API endpoint
$checkout_url = 'https://payments.yoco.com/api/checkouts';

// Prepare request data
$request_data = json_encode([
    "amount" => $amount_cents,
    "currency" => "ZAR",
   "successUrl" => "https://pharmersacademy.com/backend/payment_success.php?user_id=" . urlencode($user_id),
    "cancelUrl" => "https://pharmersacademy.com/backend/payment_cancelled.php?user_id=" . urlencode($user_id),
    "failureUrl" => "https://pharmersacademy.com/backend/payment_failed.php?user_id=" . urlencode($user_id),
]);

try {
    // Initialize cURL session
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $checkout_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $secret_key
        ],
        CURLOPT_POSTFIELDS => $request_data,
        CURLOPT_FOLLOWLOCATION => true // Follow redirects
    ]);

    // Execute request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        throw new RuntimeException('cURL error: ' . curl_error($ch));
    }

    // Get HTTP response code
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Decode response
    $response_data = json_decode($response, true);
    $checkout_id = $response_data['id'] ?? null;
    $_SESSION['checkout_id'] = $checkout_id;

    // Log response for debugging
    error_log("Yoco API Response: " . json_encode($response_data));

    if ($http_code === 200 && isset($response_data['redirectUrl'])) {
       // Insert confirmation into the database
      $status = "pending"; // Assign the string to a variable

$stmt = $conn->prepare("INSERT INTO confirmation (checkout_id, user_id, created_at, status) VALUES (?, ?, NOW(), ?)");
$stmt->bind_param("sis", $checkout_id, $user_id, $status); // Pass the variable by reference
$stmt->execute();
$stmt->close();


       // Redirect to Yoco checkout page
       header("Location: " . $response_data['redirectUrl']);
       exit();
    } else {
        $_SESSION['message'] = "Failed to create checkout. Response: " . json_encode($response_data);
        $_SESSION['messageType'] = "error";
        error_log("Checkout failed: HTTP $http_code, Response: " . json_encode($response_data));
        header("Location: payment_failed.php?user_id=" . urlencode($user_id));
        exit();
        
    }

} catch (Exception $e) {
    error_log("Checkout error: " . $e->getMessage());
    $_SESSION['message'] = "An error occurred while creating checkout.";
    $_SESSION['messageType'] = "error";
    header("Location: payment_failed.php?user_id=" . urlencode($user_id));
    exit();
}
