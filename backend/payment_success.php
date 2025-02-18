<?php
session_start();
error_reporting(E_ALL);  // Show all errors
ini_set('display_errors', 1);  // Display errors
require_once '../backend/database/db_config.php'; // Ensure database connection is available

// Check if database connection is successful
if (!$conn) {
    $_SESSION['message'] = "Database connection failed.";
    $_SESSION['messageType'] = "error";
    header("Location: register.php");
    exit();
}

$confirmation_query = $conn->prepare("SELECT user_id, checkout_id FROM confirmation WHERE status = 'pending'");
$confirmation_query->execute();
$confirmation_result = $confirmation_query->get_result();

if (!$confirmation_result->num_rows) {
    $_SESSION['message'] = "Payment confirmation not found.";
    $_SESSION['messageType'] = "error";
    header("Location: register.php");
    exit();
}

// Iterate through all confirmation records
while ($confirmation_row = $confirmation_result->fetch_assoc()) {
    $checkout_id = $confirmation_row['checkout_id'];
    $user_id = $confirmation_row['user_id']; // Get the user_id associated with the checkout_id

    // Retry checking payment status for up to 10 seconds
    for ($i = 0; $i < 10; $i++) {
        $query = $conn->prepare("SELECT status FROM payments WHERE checkout_id = ?");
        $query->bind_param("s", $checkout_id);
        $query->execute();
        $result = $query->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if ($row['status'] === 'payment.succeeded') {
                // Get the user's role from the users table
                $role_query = $conn->prepare("SELECT role FROM users WHERE id = ?");
                $role_query->bind_param("i", $user_id);
                $role_query->execute();
                $role_result = $role_query->get_result();

                if ($role_row = $role_result->fetch_assoc()) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['role'] = $role_row['role'];
                }

                // Update user status to active
                $update_user = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                $update_user->bind_param("i", $user_id);
                $update_user->execute();
                
                // Corrected: Update the confirmation status to 'complete'
                $update_confirmation = $conn->prepare("UPDATE confirmation SET status = 'complete' WHERE checkout_id = ?");
                $update_confirmation->bind_param("i", $checkout_id);
                $update_confirmation->execute();

                $_SESSION['message'] = "Payment and Registration successful!";
                $_SESSION['messageType'] = "success";
                
                header("Location: student/overview.php");
                exit();
            } elseif ($row['status'] === 'failed') {
                // Delete related records if payment failed
                $stmt = $conn->prepare("DELETE FROM user_courses WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->close();

                $delete_user = $conn->prepare("DELETE FROM users WHERE id = ?");
                $delete_user->bind_param("i", $user_id);
                $delete_user->execute();

                $_SESSION['message'] = "Payment failed. Please try again.";
                $_SESSION['messageType'] = "error";
                header("Location: register.php");
                exit();
            }
        }
        sleep(1); // Wait 1 second before retrying
    }
}

// If no confirmation after retries
$_SESSION['message'] = "Payment confirmation taking too long.";
$_SESSION['messageType'] = "warning";
header("Location: register.php");
exit();
?>
