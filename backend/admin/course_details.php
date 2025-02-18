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


if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid Course ID.";
    $_SESSION['messageType'] = "error";
    header("Location: courses.php"); // Redirect to courses page
    exit();
}

$courseId = intval($_GET['id']); // Convert to integer for safety

$stmt = $conn->prepare("SELECT course_name,description, price FROM courses WHERE id = ?");
$stmt->bind_param("i", $courseId);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    $_SESSION['message'] = "Course not found.";
    $_SESSION['messageType'] = "error";
    header("Location: courses.php");
    exit();
}
// Fetch Course Modules
$stmt = $conn->prepare("SELECT module_title FROM modules WHERE course_id = ?");
$stmt->bind_param("i", $courseId); // Bind the courseId to the query
$stmt->execute();
$result = $stmt->get_result();
$modules = $result->fetch_all(MYSQLI_ASSOC);

// Handle Course Deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_course"])) {
    // Begin Transaction
    $conn->begin_transaction();

    try {
        // Delete Course Modules First (To Maintain Foreign Key Integrity)
        $stmt = $conn->prepare("DELETE FROM modules WHERE course_id = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();

        // Delete the Course
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();

        // Commit Transaction
        $conn->commit();

        $_SESSION['message'] = "Course deleted successfully.";
        $_SESSION['messageType'] = "success";
        header("Location: courses.php?deleted=true");
        exit();
    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction if error occurs
        $_SESSION['message'] = "Error deleting course.";
        $_SESSION['messageType'] = "error";
        header("Location: courses.php"); // Redirect back in case of error
        exit();
    }
}


$enrolledQuery = "
    SELECT s.firstName, s.lastName, s.enrolment_number, s.created_at
    FROM students s
    JOIN user_courses uc ON s.id = uc.user_id
    WHERE uc.course_id = ?
    ORDER BY s.created_at DESC";

$stmt = $conn->prepare($enrolledQuery);
$stmt->bind_param("i", $courseId);
$stmt->execute();
$enrolledResult = $stmt->get_result();

$enrolledStudents = [];
while ($row = $enrolledResult->fetch_assoc()) {
    $enrolledStudents[] = $row;
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
        <div class="content-wrapper">
        <?php
                    if (!empty($message)) {
                        echo '<div id="notificationBar" class="notification-bar notification-' . $messageType . '">';
                        echo $message;
                        echo '<span class="close-btn" onclick="closeNotification()">&times;</span>';
                        echo '</div>';
                    }
                ?>
          
       
          <div class="col-12 grid-margin">
              <div class="card">
              <div class="card-body">
    <h4><b>Course:</b> <?= htmlspecialchars($course['course_name'], ENT_QUOTES, 'UTF-8') ?></h4>
    <p><b>Course price:</b>R<?= htmlspecialchars($course['price'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><b>Course description:</b> <?= htmlspecialchars($course['description'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><b>Course modules:</b></p>
    <ul class="list-ticked">
        <?php if (!empty($modules)): ?>
            <?php foreach ($modules as $module): ?>
                <li><?= htmlspecialchars($module['module_title'], ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No modules available for this course.</li>
        <?php endif; ?>
    </ul>
    
    <!-- Delete Course Button -->
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this course?');">
        <input type="hidden" name="course_id" value="<?= $courseId ?>">
        <button type="submit" name="delete_course" class="btn btn-danger mr-2">Delete Course</button>
    </form>
    <br>
    
    <!-- Add Content Button (Fixed URL) -->
    <a href="enter_content.php?id=<?= $courseId ?>">
        <button class="btn btn-primary mr-2">Add Content</button>
    </a> 
</div>

              <br>
            
            </div>
     

            <br>
            <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <p class="card-title">Enrolled Students</p>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="example" class="display expandable-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Admission Number</th>
                                        <th>Enrollment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enrolledStudents as $student): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['firstName'] . ' ' . $student['lastName']); ?></td>
                                            <td class="font-weight-bold"><?= htmlspecialchars($student['enrolment_number']); ?></td>
                                           <td><?= date("d M Y", strtotime($student['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

