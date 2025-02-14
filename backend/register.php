<?php
session_start();
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
  <title>Register</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="./vendors/feather/feather.css">
  <link rel="stylesheet" href="./vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="./vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
   <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="./css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="../img/logo.jpg" />
<style>
  form {
      max-width: 700px;
      margin: 0 auto;
      background: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .form-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
    }

    input[type="text"],
    input[type="email"],
    textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    textarea {
      resize: vertical;
    }

    .form-check {
      grid-column: 1 / -1;
      margin-top: 15px;
    }

    button {
      background-color:#51c944;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      grid-column: 1 / -1;
    }

    button:hover {
      background-color: green;
    }

    .text-center {
      text-align: center;
      margin-top: 20px;
    }

    .text-primary {
      color: #007bff;
      text-decoration: none;
    }

    .text-primary:hover {
      text-decoration: underline;
    }

    /* Responsive for smaller screens */
    @media (max-width: 600px) {
      .form-section {
        grid-template-columns: 1fr;
      }
    }
</style>
</head>

<body>
  <div class="container-scroller" >
    <div class="container-fluid page-body-wrapper full-page-wrapper" >
      <div class="content-wrapper d-flex align-items-center auth px-0" >
        <div class="row w-100 mx-0" >
          <form class="col-lg-8 mx-auto" method="post" action="create.php">
          <?php
if (!empty($message)) {
    echo '<div id="notificationBar" class="notification-bar notification-' . $messageType . '">';
    echo $message;
    echo '<span class="close-btn" onclick="closeNotification()">&times;</span>';
    echo '</div>';
}
?>
            <div class="auth-form-light text-left py-5 px-4 px-sm-5" >
            <div class="brand-logo">
                <img src="../img/logo.jpg" alt="logo">
              </div>
              <h4>Participant Registration Form</h4>
              <h6 class="font-weight-light">Signing up is easy. It only takes a few steps</h6>
          
              <div class="form-section">
      <div>
        <label for="title">Participant Title:</label>
        <input type="text" id="title" name="title" required>
      </div>
      
      <div>
        <label for="first-name">First Name:</label>
        <input type="text" id="first-name" name="firstName" required>
      </div>

      <div>
        <label for="last-name">Last Name:</label>
        <input type="text" id="last-name" name="lastName" required>
      </div>
      

      <div>
        <label for="company">Company:</label>
        <input type="text" id="company" name="company" required>
      </div>

      <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div>
        <label for="contact-number">Contact Number:</label>
        <input type="text" id="contact-number" name="contact" required>
      </div>

      <div>
        <label for="location">Country / City:</label>
        <input type="text" id="location" name="location" required>
      </div>

      <div>
        <label for="degree">Degree / Qualification:</label>
        <input type="text" id="degree" name="degree">
      </div>

      <div>
        <label for="enrolment-number">Returning Learner Enrolment Number:</label>
        <input type="text" id="enrolment-number" name="return_number">
      </div>

      <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username">
      </div>

       <div>
        <label for="password">Password:</label>
        <input type="text" id="" name="password">
      </div>

      <div style="grid-column: 1 / -1;">
        <label for="course-selection">Course Selection:</label>
        <textarea id="course-selection" name="course" rows="4" placeholder="List your selected courses here..."></textarea>
      </div>
    </div>

    <div class="form-check">
      <label>
        <input type="checkbox" required> I agree to all Terms & Conditions
      </label>
      <p><strong>NB:</strong> R500 enrolment fee applies to all new learners.</p>
    </div>

    <button type="submit">Sign Up</button>

    <div class="text-center">
      <p>Already have an account? <a href="login.php" class="text-primary">Login</a></p>
    </div>
    <div class="text-center mt-4 font-weight-light">
                 <a href="../index.html" class="text-primary">Go back home</a>
                </div>
  
            </div>
  </form>
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
  <script src="script.js"></script>
  <!-- endinject -->
</body>

</html>
