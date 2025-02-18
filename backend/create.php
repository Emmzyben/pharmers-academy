<?php
session_start();
require_once '../backend/database/db_config.php';

$message = '';

function generateAdmissionNumber() {
    return 'PHAD-' . strtoupper(bin2hex(random_bytes(4)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $participant_title = $_POST['title'] ?? '';
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $company = $_POST['company'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $location = $_POST['location'] ?? '';
    $degree = $_POST['degree'] ?? '';
    $license = $_POST['license'] ?? '';
    $return_number = $_POST['return_number'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = 'student';
    $status = 'inactive';
    $full_name = trim("$firstName $lastName");

    // Ensure selected courses are properly received
    $selected_courses = $_POST['selected_courses'] ?? [];
    $total_price = ($_POST['total_price'] ?? 0.0);

    // If return_number is empty, add 500 to total_price
    if (empty($return_number)) {
        $total_price += 500;
    }

    if (!is_array($selected_courses) || empty($selected_courses)) {
        $_SESSION['message'] = "Please select at least one course.";
        $_SESSION['messageType'] = "error";
        header("Location: register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['messageType'] = "error";
        header("Location: register.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Start transaction
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE (email = ? OR username = ?) AND status = 'active'");
        $stmt->bind_param("ss", $email, $username); // Ensure correct order
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            throw new Exception("Email or Username already exists.");
        }
        $stmt->close();
        

        $admissionNumber = generateAdmissionNumber();

        // Insert into `users`
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, username, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $full_name, $email, $username, $hashedPassword, $role, $status);
        $stmt->execute();
        $userId = $stmt->insert_id;
        $stmt->close();

        // Insert into `students`
        $stmt = $conn->prepare("INSERT INTO students (id, title, firstName, lastName, company, contact, location, degree, return_number, enrolment_number, license) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssssss", $userId, $participant_title, $firstName, $lastName, $company, $contact, $location, $degree, $return_number, $admissionNumber, $license);
        $stmt->execute();
        $stmt->close();

        // Insert selected courses into `user_courses`
        $stmt = $conn->prepare("INSERT INTO user_courses (user_id, course_id, course_price) VALUES (?, ?, ?)");
        foreach ($selected_courses as $course) {
            $courseData = explode('|', $course);
            if (count($courseData) !== 2) {
                throw new Exception("Invalid course data.");
            }
            list($course_id, $course_price) = $courseData;
            $course_id = intval($course_id);
            $course_price = floatval($course_price);
            $stmt->bind_param("iid", $userId, $course_id, $course_price);
            $stmt->execute();
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['messageType'] = "error";
    }

    $conn->close();
    header("Location: create_checkout.php?total_price=$total_price&user_id=$userId&email=$email");
    exit();
}
?>
