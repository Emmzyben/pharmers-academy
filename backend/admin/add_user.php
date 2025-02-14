<?php
session_start();
require_once '../database/db_config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $role = htmlspecialchars(trim($_POST['role']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    $full_name = trim("$first_name $last_name");

    if ($first_name && $last_name && $role && $username && $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['message'] = "Username already exists.";
            $_SESSION['messageType'] = "error";
            $stmt->close();
            $conn->close();
            header("Location: add_user.php");
            exit();
        }
        $stmt->close();

        $profilePicturePath = null;

        if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
            $uploadDir = '../../img/';
            $fileTmpPath = $_FILES['profilePicture']['tmp_name'];
            $fileName = $_FILES['profilePicture']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($fileExtension, $allowedExtensions)) {
                $_SESSION['message'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
                $_SESSION['messageType'] = "error";
                header("Location: add_user.php");
                exit();
            }

            $uniqueFileName = uniqid() . '.' . $fileExtension;
            $profilePicturePath = $uploadDir . $uniqueFileName;

            if (!move_uploaded_file($fileTmpPath, $profilePicturePath)) {
                $_SESSION['message'] = "Failed to upload the profile picture.";
                $_SESSION['messageType'] = "error";
                header("Location: add_user.php");
                exit();
            }
        }

        $query = "INSERT INTO users (full_name, role, username, password, profile_picture) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("sssss", $full_name, $role, $username, $hashedPassword, $profilePicturePath);

            if ($stmt->execute()) {
                $message = "User added successfully!";
                $messageType = "success";
            } else {
                $message = "Error: " . $stmt->error;
                $messageType = "error";
            }
            $stmt->close();
        } else {
            $message = "Error preparing query: " . $conn->error;
            $messageType = "error";
        }
    } else {
        $message = "Invalid input data. Please check your entries.";
        $messageType = "error";
    }

    $conn->close();
    $_SESSION['message'] = $message;
    $_SESSION['messageType'] = $messageType;
    header("Location: add_user.php");
    exit();
}

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
  <title>Add User</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="img/logo.jpg" />
  <link rel="stylesheet" href="../style.css">
</head>
<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="index.html"><img src="img/logo.jpg" class="mr-2" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="index.html"><img src="img/logo.jpg"  alt="logo" style="width:400px"/></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav mr-lg-2">
         
        </ul>
        <ul class="navbar-nav navbar-nav-right">
          
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="images/faces/face28.jpg" alt="profile"/>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item">
                <i class="ti-settings text-primary"></i>
                Settings
              </a>
              <a class="dropdown-item" href="logout.php">
                <i class="ti-power-off text-primary"></i>
                Logout
              </a>
            </div>
          </li>
        
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="ti-settings"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close ti-close"></i>
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options selected" id="sidebar-light-theme"><div class="img-ss rounded-circle bg-light border mr-3"></div>Light</div>
          <div class="sidebar-bg-options" id="sidebar-dark-theme"><div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark</div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles success"></div>
            <div class="tiles warning"></div>
            <div class="tiles danger"></div>
            <div class="tiles info"></div>
            <div class="tiles dark"></div>
            <div class="tiles default"></div>
          </div>
        </div>
      </div>
    
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="overview.php">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link"   href="students.php" aria-expanded="false" aria-controls="ui-basic">
              <i class="icon-layout menu-icon"></i>
              <span class="menu-title">Students</span>
              <i class="menu-arrow"></i>
            </a>
           
          </li>
          <li class="nav-item">
            <a class="nav-link"   href="instructor.php" aria-expanded="false" aria-controls="form-elements">
              <i class="icon-columns menu-icon"></i>
              <span class="menu-title">Instructors</span>
              <i class="menu-arrow"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link"   href="courses.php" aria-expanded="false" aria-controls="charts">
              <i class="icon-bar-graph menu-icon"></i>
              <span class="menu-title">Courses</span>
              <i class="menu-arrow"></i>
            </a>
          </li>
        
          <li class="nav-item">
            <a class="nav-link"   href="settings.php">
              <i class="icon-head menu-icon"></i>
              <span class="menu-title">User Accounts</span>
              <i class="menu-arrow"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link"  href="logout.php">
              <i class="icon-contract menu-icon"></i>
              <span class="menu-title">Log Out</span>
              <i class="menu-arrow"></i>
            </a>
         
          </li>
        </ul>
      </nav>
      <!-- partial -->
      <div class="main-panel">
      <?php
                    if (!empty($message)) {
                        echo '<div id="notificationBar" class="notification-bar notification-' . $messageType . '">';
                        echo $message;
                        echo '<span class="close-btn" onclick="closeNotification()">&times;</span>';
                        echo '</div>';
                    }
                ?>
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Add User</h3>
                  <h6 class="font-weight-normal mb-0">Enter data to add</h6>
                </div>
               
              </div>
            </div>
          </div>
          <div class="col-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                <form class="forms-sample" method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="first_name">First name</label>
        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Name" required>
    </div>
    <div class="form-group">
        <label for="last_name">Last name</label>
        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Name" required>
    </div>
    <div class="form-group">
        <label for="role">Role</label>
        <select class="form-control" id="role" name="role" required>
            <option value="">Select Role</option>
            <option value="Admin">Admin</option>
            <option value="Student">Student</option>
            <option value="Instructor">Instructor</option>
        </select>
    </div>
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
    </div>
    <div class="form-group">
    <label for="profilePicture">File upload</label>
    <input type="file" name="profilePicture" id="profilePicture" class="file-upload-default" style="display: none;" required>
    <div class="input-group col-xs-12">
        <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
        <span class="input-group-append">
            <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
        </span>
    </div>
</div>

<script>
    // Trigger file input dialog when clicking the custom upload button
    document.querySelector('.file-upload-browse').addEventListener('click', function () {
        document.getElementById('profilePicture').click();
    });

    // Update text input with selected file name
    document.getElementById('profilePicture').addEventListener('change', function () {
        const fileName = this.files[0]?.name || "No file chosen";
        document.querySelector('.file-upload-info').value = fileName;
    });
</script>

    <button type="submit" class="btn btn-primary mr-2">Submit</button>
</form>

                </div>
              </div>
            </div>
       </div>
       
      <!-- main-panel ends -->
    </div>   
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <script src="js/dataTables.select.min.js"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="js/dashboard.js"></script>
  <script src="js/Chart.roundedBarCharts.js"></script>
  <!-- End custom js for this page-->
  <script src="../script.js"></script>
</body>

</html>

