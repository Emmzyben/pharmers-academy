<?php
session_start();

$message = '';

function generateAdmissionNumber() {
    return 'PHAD-' . strtoupper(bin2hex(random_bytes(4)));
}

include './database/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $participant_title = $_POST['title'] ?? '';
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $company = $_POST['company'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $location = $_POST['location'] ?? '';
    $degree = $_POST['degree'] ?? '';
    $return_number = $_POST['return_number'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $course = $_POST['course'] ?? '';
    $role = 'student'; // Assuming 'student' as default role
    $full_name = trim("$firstName $lastName");

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($contact) || empty($degree) || empty($course) || empty($username) || empty($password)) {
        $_SESSION['message'] = "Please fill in all required fields.";
        $_SESSION['messageType'] = "error";
        header("Location: register.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "Username already exists.";
        $_SESSION['messageType'] = "error";
        $stmt->close();
        $conn->close();
        header("Location: register.php");
        exit();
    }
    $stmt->close();

    $admissionNumber = generateAdmissionNumber();

    // Insert into `users`
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, username, password, role) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssss", $full_name, $email, $username, $hashedPassword, $role);
        if ($stmt->execute()) {
            $userId = $stmt->insert_id;

            // Insert into `students`
            $stmt2 = $conn->prepare("INSERT INTO students (id, title, firstName, lastName, company, contact, location, degree, return_number, course, enrolment_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt2) {
                $stmt2->bind_param("issssssssss", $userId, $participant_title, $firstName, $lastName, $company, $contact, $location, $degree, $return_number, $course, $admissionNumber);
                if ($stmt2->execute()) {
                    $_SESSION['message'] = "Registration successful.";
                    $_SESSION['messageType'] = "success";
                } else {
                    // Rollback by deleting user if student registration fails
                    $conn->query("DELETE FROM users WHERE id = $userId");
                    $_SESSION['message'] = "Error inserting student record: " . $stmt2->error;
                    $_SESSION['messageType'] = "error";
                }
                $stmt2->close();
            } else {
                $conn->query("DELETE FROM users WHERE id = $userId");
                $_SESSION['message'] = "Error preparing student statement: " . $conn->error;
                $_SESSION['messageType'] = "error";
            }
        } else {
            $_SESSION['message'] = "Error inserting user record: " . $stmt->error;
            $_SESSION['messageType'] = "error";
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Error preparing user statement: " . $conn->error;
        $_SESSION['messageType'] = "error";
    }

    $conn->close();
    header("Location: student/overview.php");
    exit();
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'];
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
}
?>
