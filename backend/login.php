<?php
session_start(); // Start the session at the very top of the file
require_once './database/db_config.php';

// Initialize message variables
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $emailOrUsername = trim($_POST['email-username']);
    $password = trim($_POST['password']);

    // Validate input fields
    if (empty($emailOrUsername) || empty($password)) {
        $message = "Email/Username and Password are required.";
        $messageType = "error";
    } else {
        // Check for the user in the database
        $query = "SELECT * FROM users WHERE (email = ? OR username = ?) AND status = 'active'";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Store user data in session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect based on user role
                    switch ($user['role']) {
                        case 'admin':
                            header("Location: admin/overview.php");
                            break;
                        case 'instructor':
                            header("Location: instructor/overview.php");
                            break;
                        case 'student':
                            header("Location: student/overview.php");
                            break;
                        default:
                            header("Location: login.php");
                            break;
                    }
                    exit;
                } else {
                    $message = "Invalid password. Please try again.";
                    $messageType = "error";
                }
            } else {
                $message = "No user found with the provided email/username.";
                $messageType = "error";
            }

            // Close the statement
            $stmt->close();
        } else {
            $message = "Database query error. Please try again later.";
            $messageType = "error";
        }
    }

    // Store message in the session for display
    $_SESSION['message'] = $message;
    $_SESSION['messageType'] = $messageType;

    // Redirect back to the login page
    header("Location: login.php");
    exit;
}

// Retrieve and unset messages from the session (if any)
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'];
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="./vendors/feather/feather.css">
  <link rel="stylesheet" href="./vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="./vendors/css/vendor.bundle.base.css">
<link rel="stylesheet" href="style.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="./css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="../img/logo.jpg" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
          <?php
if (!empty($message)) {
    echo '<div id="notificationBar" class="notification-bar notification-' . $messageType . '">';
    echo $message;
    echo '<span class="close-btn" onclick="closeNotification()">&times;</span>';
    echo '</div>';
}
?>

            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img src="../img/logo.jpg" alt="logo">
              </div>
              <h4>Hello! let's get started</h4>
              <h6 class="font-weight-light">Sign in to continue.</h6>
              <form class="pt-3" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                  <input type="text" name="email-username" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Username or email">
                </div>
               <div class="form-group">
  <div style="position: relative;">
    <input type="password" name="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password">
    <span id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
      👁️
    </span>
  </div>
</div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" >SIGN IN</button>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input">
                      Keep me signed in
                    </label>
                  </div>
                  <a href="#" class="auth-link text-black">Forgot password?</a>
                </div>
               
                <div class="text-center mt-4 font-weight-light">
                  Don't have an account? <a href="register.php" class="text-primary">Create</a>
                </div>
                <div class="text-center mt-4 font-weight-light">
                 <a href="../index.php" class="text-primary">Go back home</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="./vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="./js/off-canvas.js"></script>
  <script src="./js/hoverable-collapse.js"></script>
  <script src="./js/template.js"></script>
  <script src="./js/settings.js"></script>
  <script src="./js/todolist.js"></script>
  <!-- endinject -->
   <script src="script.js"></script>
   <script>
  document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordInput = document.getElementById("exampleInputPassword1");
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      this.innerText = "👁️‍🗨️"; // Change icon when password is visible
    } else {
      passwordInput.type = "password";
      this.innerText = "👁️"; // Change icon back when hidden
    }
  });
</script>
</body>

</html>
