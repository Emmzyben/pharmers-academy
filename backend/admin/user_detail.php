<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: logout.php"); 
  exit;
}


include '../database/db_config.php'; 
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  $_SESSION['message'] = "User not found.";
                $_SESSION['messageType'] = "error";
}
$stmt->close();


// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$message = '';
$messageType = '';

// Fetch student and user details
$id = $_GET['id'] ?? 0;
$admin = [];
$admin2 = [];

if ($id) {
  

    // Fetch user details
    $stmt = $conn->prepare("SELECT id, username, email, password, profile_picture FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($admin2_id, $admin2_username, $admin2_email, $admin2_password, $admin2_profile_picture);
        $stmt->fetch();
        $admin2 = [
            'id' => $admin2_id,
            'username' => $admin2_username,
            'password' => $admin2_password,
            'email' => $admin2_email,
            'profile_picture' => $admin2_profile_picture
        ];
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check if username already exists for a different user
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        setSessionMessage("Username already exists.", "error");
        $stmt->close();
        header("Location: user_detail.php?id=$id");
        exit();
    }
    $stmt->close();

    $profilePicturePath = null;

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $uploadDir = '../../img';
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate file extension
        if (!in_array($fileExtension, $allowedExtensions)) {
            setSessionMessage("Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.", "error");
            header("Location: user_detail.php");
            exit();
        }

        // Create a unique file name and move the file
        $uniqueFileName = uniqid() . '.' . $fileExtension;
        $profilePicturePath = $uploadDir . $uniqueFileName;

        if (!move_uploaded_file($fileTmpPath, $profilePicturePath)) {
            setSessionMessage("Failed to upload the profile picture.", "error");
            header("Location: user_detail.php");
            exit();
        }
    }

 

    // Update user details
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, password=?, email=?, profile_picture=? WHERE id=?");
        $stmt->bind_param("ssssi", $username, $hashedPassword, $email, $profilePicturePath, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, profile_picture=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $profilePicturePath, $id);
    }

    if ($stmt->execute()) {
        setSessionMessage("user record updated successfully.", "success");
        $stmt->close();
        header("Location: settings.php");
        exit();
    } else {
        setSessionMessage("Error updating user record: " . $stmt->error, "error");
        $stmt->close();
        header("Location: user_detail.php?id=$id");
        exit();
    }
}

// Function to handle session messages
function setSessionMessage($message, $messageType) {
    $_SESSION['message'] = $message;
    $_SESSION['messageType'] = $messageType;
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
  <title>user details</title>
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
    <!-- partial:partials/_navbar.php -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="index.php"><img src="img/logo.jpg" class="mr-2" alt="logo"/></a>
        <a class="navbar-brand brand-logo-mini" href="index.php"><img src="img/logo.jpg"  alt="logo" style="width:400px"/></a>
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
              <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="image">
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="settings.php">
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
      <!-- partial:partials/_settings-panel.php -->
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
      <!-- partial:partials/_sidebar.php -->
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
                  <h3 class="font-weight-bold">User details</h3>
                  <h6 class="font-weight-normal mb-0">View/edit User details</h6>
                </div>
               
              </div>
            </div>
          </div>
          
       
          <div class="col-12 grid-margin">
              <div class="card">
                <div class="card-body">
                  <form class="form-sample">
                    
                    <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                          <div class="col-sm-9">
                          <?php if (!empty($admin2['profile_picture'])): ?>
        <img src="../profileImage/<?= htmlspecialchars($admin2['profile_picture']) ?>" alt="Profile Picture" width="200px" style="border-radius:10px">
    <?php else: ?>
      <img src="img/carousel-1.jpg" alt="" width="200px" style="border-radius:10px">
    <?php endif; ?>
                          </div>
                        </div>
                      </div>

                     





                    </div>
                    
                  </form>
                 
                </div>
              </div>
              <br>
              <div class="card">
                <div class="card-body">
                <form method="post" action="" enctype="multipart/form-data">
  
    <div class="row">
        <div class="col-md-6">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Username</label>
                <div class="col-sm-9">
                    <input type="text" name='username' class="form-control" value="<?php echo htmlspecialchars($admin2['username']); ?>">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-9">
                    <input type="text" name='email' class="form-control" value="<?php echo htmlspecialchars($admin2['email']); ?>">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-9">
                    <input type="text" name='password' class="form-control" value="">
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Change Profile Picture</label>
            <div class="col-sm-9">
                <input type="file" name="profile_picture" class="form-control" id="profile_picture">
            </div>
            <div>
                <!-- Image preview -->
                <img id="img-preview" src="#" alt="Image Preview" style="max-width: 200px; display: none;max-height:150px">
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-sm-9">
                <button type="submit" class="btn btn-primary mr-2">Update Details</button>
                <button class="btn btn-danger mr-2">Delete student</button>
            </div>
        </div>
    </div>
</form>

                 
                </div>
              </div>
            </div>
            <script>
  document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(event) {
        const imgPreview = document.getElementById('img-preview');
        imgPreview.src = event.target.result;
        imgPreview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  });
</script>
         
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.php -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2025.  Pharmers academy. All rights reserved.</span>
         </div>
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Developed by <a href="" target="_blank">Argon tech</a></span> 
          </div>
        </footer> 
        <!-- partial -->
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

