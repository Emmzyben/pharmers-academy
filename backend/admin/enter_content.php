<?php
session_start();
require_once '../database/db_config.php';

$message = "";
$messageType = "";
$courseId = intval($_GET['id']); 

// Fetch all modules for the dropdown
$modules = [];
$queryModules = "SELECT id, module_title FROM modules WHERE course_id = ?";
$stmt = $conn->prepare($queryModules);
$stmt->bind_param("i", $courseId);
$stmt->execute();
$resultModules = $stmt->get_result();

if ($resultModules->num_rows > 0) {
    while ($row = $resultModules->fetch_assoc()) {
        $modules[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $moduleId = $_POST['moduleId'] ?? '';
    $contentType = $_POST['contentType'] ?? '';
    $contentData = '';

    if (!empty($moduleId) && !empty($contentType)) {
        if ($contentType === "video file" || $contentType === "Pdf") {
            // Handle file upload
            if (isset($_FILES['contentFile']) && $_FILES['contentFile']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['contentFile']['tmp_name'];
                $fileName = basename($_FILES['contentFile']['name']);
                $uploadDir = '../../img'; // Ensure this directory exists and is writable
                $destinationPath = $uploadDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                    $contentData = $destinationPath; // Store file path in database
                } else {
                    $message = "Error uploading file.";
                    $messageType = "error";
                }
            } else {
                $message = "No file uploaded.";
                $messageType = "error";
            }
        } elseif ($contentType === "video link") {
            // Handle URL input
            $contentData = htmlspecialchars(trim($_POST['contentLink'] ?? ''));
        }

        if (!empty($contentData)) {
            // Insert content into database
            $queryContent = "INSERT INTO contents (module_id, content_type, content_data) VALUES (?, ?, ?)";
            $stmtContent = $conn->prepare($queryContent);

            if ($stmtContent) {
                $stmtContent->bind_param("iss", $moduleId, $contentType, $contentData);

                if ($stmtContent->execute()) {
                    $message = "Content added successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error inserting content: " . $stmtContent->error;
                    $messageType = "error";
                }
                $stmtContent->close();
            } else {
                $message = "Error preparing content query: " . $conn->error;
                $messageType = "error";
            }
        }
    } else {
        $message = "Invalid input data. Please fill out all fields.";
        $messageType = "error";
    }

    $_SESSION['message'] = $message;
    $_SESSION['messageType'] = $messageType;
    header("Location:  enter_content.php?id=$courseId");
    exit;
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
  <title>Add Content</title>
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
        <div class="content-wrapper">
        <?php
                    if (!empty($message)) {
                        echo '<div id="notificationBar" class="notification-bar notification-' . $messageType . '">';
                        echo $message;
                        echo '<span class="close-btn" onclick="closeNotification()">&times;</span>';
                        echo '</div>';
                    }
                ?>
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Add Content</h3>
                  <h6 class="font-weight-normal mb-0">add course and module content</h6>
                </div>
               
              </div>
            </div>
          </div>
          
       
          <div class="col-12 grid-margin">
          <form action="" method="POST" enctype="multipart/form-data" class="forms-sample">
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="courseName">Module</label>
                <select name="moduleId" class="form-control">
                    <option value="">Select Module</option>
                    <?php foreach ($modules as $module) : ?>
                        <option value="<?= $module['id'] ?>"><?= $module['module_title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="contentType">Content type</label>
                <select name="contentType" class="form-control" id="contentType">
                    <option value="">Select option</option>
                    <option value="video file">Video</option>
                    <option value="Pdf">PDF file</option>
                    <option value="video link">Video Link</option>
                </select>
            </div>

            <div class="form-group" id="fileUploadGroup" style="display:none;">
                <label for="contentFile">Upload File</label>
                <input type="file" name="contentFile" class="form-control">
            </div>

            <div class="form-group" id="linkInputGroup" style="display:none;">
                <label for="contentLink">Video Link</label>
                <input type="text" name="contentLink" class="form-control">
            </div>
        </div>
    </div>

    <br>
    <div class="card">
        <div class="card-body">
            <button type="submit" class="btn btn-primary mr-2">Post</button>
        </div>
    </div>
</form>

<script>
document.getElementById("contentType").addEventListener("change", function() {
    var selectedType = this.value;
    document.getElementById("fileUploadGroup").style.display = selectedType === "video file" || selectedType === "Pdf" ? "block" : "none";
    document.getElementById("linkInputGroup").style.display = selectedType === "video link" ? "block" : "none";
});
</script>


            </div>
          
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
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

