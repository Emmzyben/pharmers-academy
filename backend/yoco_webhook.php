<?php
declare(strict_types=1);

require_once '../backend/database/db_config.php';

// Configure error reporting and logging
ini_set('display_errors', 'stderr');
ini_set('log_errors', 'On');
ini_set('error_log', 'yoco_webhook.error.log');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    header('Content-Type: application/json');

    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new RuntimeException('Invalid request method');
    }

    // Read and decode raw JSON input
    $rawData = file_get_contents('php://input');
    if ($rawData === false || empty($rawData)) {
        throw new RuntimeException('Empty webhook payload received');
    }

    // Log raw webhook data for debugging
    file_put_contents('yoco_raw.log', $rawData . "\n", FILE_APPEND);

    $event = json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);

    // Log parsed event
    file_put_contents('yoco_debug.log', json_encode($event, JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

    // Validate event type
    if (!isset($event['type']) || $event['type'] !== 'payment.succeeded') {
        throw new RuntimeException('Invalid or missing event type');
    }

    // Validate required fields
    if (!isset(
        $event['id'],
        $event['payload']['metadata']['checkoutId'],
        $event['payload']['amount'],
        $event['payload']['currency'],
        $event['payload']['status']
    )) {
        throw new RuntimeException('Missing required fields in event payload');
    }

    // Extract payment data
    $paymentData = [
        'checkout_id'   => $event['payload']['metadata']['checkoutId'],
        'payment_id'    => $event['id'],
        'amount'        => $event['payload']['amount'],
        'currency'      => $event['payload']['currency'],
        'status'        => $event['type'],
        'payment_method' => $event['payload']['paymentMethodDetails']['type'] ?? null,
        'masked_card'   => $event['payload']['paymentMethodDetails']['card']['maskedCard'] ?? null,
        'card_scheme'   => $event['payload']['paymentMethodDetails']['card']['scheme'] ?? null,
        'raw_payload'   => json_encode($event, JSON_THROW_ON_ERROR)
    ];

    // Insert payment record into the database
    $stmt = $conn->prepare(
        "INSERT INTO payments 
            (checkout_id, payment_id, amount, currency, status, 
             payment_method, masked_card, card_scheme, raw_payload, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
    );

    // Ensure all values are treated as strings
    $stmt->bind_param(
        "sssssssss",
        $paymentData['checkout_id'],
        $paymentData['payment_id'],
        $paymentData['amount'],
        $paymentData['currency'],
        $paymentData['status'],
        $paymentData['payment_method'],
        $paymentData['masked_card'],
        $paymentData['card_scheme'],
        $paymentData['raw_payload']
    );

    $stmt->execute();
    $stmt->close();

    // Log successful payment processing
    file_put_contents(
        'yoco_webhook.log',
        sprintf("[%s] Payment processed successfully: %s\n", date('Y-m-d H:i:s'), json_encode($paymentData)),
        FILE_APPEND
    );

    http_response_code(200);
    echo json_encode(['status' => 'success']);

} catch (JsonException $e) {
    logError("Invalid JSON payload: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload']);
} catch (RuntimeException $e) {
    logError("Runtime error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
} catch (mysqli_sql_exception $e) {
    logError("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database operation failed']);
} catch (Throwable $e) {
    logError("Unexpected error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred']);
} finally {
    // Close DB connection if open
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}

// Function to log errors
function logError(string $message): void {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = sprintf("[%s] Error: %s\n", $timestamp, $message);
    file_put_contents('yoco_webhook.error.log', $logMessage, FILE_APPEND);
}
