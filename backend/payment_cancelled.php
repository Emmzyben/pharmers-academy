<?php
session_start();
require_once '../backend/database/db_config.php';

$user_id = $_GET['user_id'] ?? 0;

if (!$user_id) {
    $_SESSION['message'] = "Invalid user ID.";
    $_SESSION['messageType'] = "error";
    header("Location: register.php");
    exit();
}

$conn->begin_transaction();

try {
   // Also delete any selected courses in `user_courses`
   $stmt = $conn->prepare("DELETE FROM user_courses WHERE user_id = ?");
   $stmt->bind_param("i", $user_id);
   $stmt->execute();
   $stmt->close();

// Delete user from `users`
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

   $conn->commit();

    $_SESSION['message'] = "Payment was cancelled. Registration unsuccessfull.";
    $_SESSION['messageType'] = "error";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['message'] = "Error: Unable to remove registration. " . $e->getMessage();
    $_SESSION['messageType'] = "error";
}

$conn->close();
header("Location: register.php");
exit();
?>
