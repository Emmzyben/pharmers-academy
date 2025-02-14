<?php
session_start();
require_once '../database/db_config.php';

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $courseName = htmlspecialchars(trim($_POST['course']));
    $description = htmlspecialchars(trim($_POST['description']));
    $modules = $_POST['modules'] ?? [];
    $descriptions = $_POST['descriptions'] ?? [];

    if (!empty($courseName) && !empty($description) && is_array($modules) && is_array($descriptions)) {
        // Insert the course into the database first
        $queryCourse = "INSERT INTO courses (course_name, description) VALUES (?, ?)";
        $stmtCourse = $conn->prepare($queryCourse);

        if ($stmtCourse) {
            $stmtCourse->bind_param("ss", $courseName, $description);

            if ($stmtCourse->execute()) {
                $courseId = $stmtCourse->insert_id; // Get inserted course ID

                // Insert each module with its description
                $queryModule = "INSERT INTO modules (course_id, module_title, module_description) VALUES (?, ?, ?)";
                $stmtModule = $conn->prepare($queryModule);

                if ($stmtModule) {
                    for ($i = 0; $i < count($modules); $i++) {
                        $moduleTitle = htmlspecialchars(trim($modules[$i]));
                        $moduleDescription = htmlspecialchars(trim($descriptions[$i]));

                        if (!empty($moduleTitle) && !empty($moduleDescription)) {
                            $stmtModule->bind_param("iss", $courseId, $moduleTitle, $moduleDescription);
                            $stmtModule->execute();
                        }
                    }
                    $stmtModule->close();
                    $message = "Course and modules added successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error preparing query for modules: " . $conn->error;
                    $messageType = "error";
                }
            } else {
                $message = "Error inserting course: " . $stmtCourse->error;
                $messageType = "error";
            }
            $stmtCourse->close();
        } else {
            $message = "Error preparing course query: " . $conn->error;
            $messageType = "error";
        }
    } else {
        $message = "Invalid input data. Please fill out all fields.";
        $messageType = "error";
    }

    $conn->close();
    $_SESSION['message'] = $message;
    $_SESSION['messageType'] = $messageType;
    header("Location: add_course.php");
    exit;
}

// Display session messages
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
  <title>Add Course</title>
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
                  <h3 class="font-weight-bold">Add Course</h3>
                  <h6 class="font-weight-normal mb-0">Enter Course name and course modules</h6>
                </div>
               
              </div>
            </div>
          </div>
          
       
          <div class="col-12 grid-margin">
          <form action="" method="POST" class="forms-sample">
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="courseName">Course Name</label>
                <input type="text" class="form-control" id="courseName" name="course" placeholder="Enter name" required>
            </div>
            <div class="form-group">
                <label for="courseDescription">Description</label>
                <textarea class="form-control" id="courseDescription" name="description" rows="4" placeholder="Enter Description" required></textarea>
            </div>

            <!-- Modules Section -->
            <div id="modules">
                <div class="module-group">
                    <div class="form-group">
                        <label>Module Title</label>
                        <input type="text" class="form-control" name="modules[]" placeholder="Enter module title" required>
                    </div>
                    <div class="form-group">
                        <label>Module Description</label>
                        <input type="text" class="form-control" name="descriptions[]" placeholder="Enter module description" required>
                    </div>
                </div>
            </div>

            <!-- Add Module Button -->
            <button type="button" class="btn btn-secondary mr-2" onclick="addModuleField()">Add Module</button>
        </div>
    </div>

    <br>
    <div class="card">
        <div class="card-body">
            <button type="submit" class="btn btn-primary mr-2">Submit</button>
        </div>
    </div>
</form>

<script>
    function addModuleField() {
        const modulesDiv = document.getElementById("modules");
        const moduleGroup = document.createElement("div");
        moduleGroup.classList.add("module-group");

        moduleGroup.innerHTML = `
            <div class="form-group">
                <label>Module Title</label>
                <input type="text" class="form-control" name="modules[]" placeholder="Enter module title" required>
            </div>
            <div class="form-group">
                <label>Module Description</label>
                <input type="text" class="form-control" name="descriptions[]" placeholder="Enter module description" required>
            </div>
            <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removeModuleField(this)">Remove Module</button>
            <hr>
        `;

        modulesDiv.appendChild(moduleGroup);
    }

    function removeModuleField(button) {
        button.parentElement.remove();
    }
</script>

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

