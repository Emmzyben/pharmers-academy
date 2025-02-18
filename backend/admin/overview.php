<?php 
session_start();
// Check if the user is logged in
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
    $message = "User not found.";
    $messageType = "error";
}
$stmt->close();


// Get total number of users
$userQuery = "SELECT COUNT(*) AS total_users FROM users";
$userResult = $conn->query($userQuery);
$totalUsers = $userResult->fetch_assoc()['total_users'];

// Get total number of students
$studentQuery = "SELECT COUNT(*) AS total_students FROM users WHERE role = 'student'";
$studentResult = $conn->query($studentQuery);
$totalStudents = $studentResult->fetch_assoc()['total_students'];

// Get total number of instructors
$instructorQuery = "SELECT COUNT(*) AS total_instructors FROM users WHERE role = 'instructor'";
$instructorResult = $conn->query($instructorQuery);
$totalInstructors = $instructorResult->fetch_assoc()['total_instructors'];

// Get total number of courses
$courseQuery = "SELECT COUNT(*) AS total_courses FROM courses";
$courseResult = $conn->query($courseQuery);
$totalCourses = $courseResult->fetch_assoc()['total_courses'];

// Fetch all courses and their enrolled student count
$coursesQuery = "SELECT id, course_name FROM courses";
$coursesResult = $conn->query($coursesQuery);

$courses = [];
while ($course = $coursesResult->fetch_assoc()) {
    $courseId = $course['id'];

    // Count students enrolled in this course
    $enrollmentQuery = "SELECT COUNT(*) AS student_count FROM user_courses WHERE course_id = $courseId";
    $enrollmentResult = $conn->query($enrollmentQuery);
    $studentCount = $enrollmentResult->fetch_assoc()['student_count'];

    // Store course data with student count
    $courses[] = [
        'id' => $courseId,
        'name' => $course['course_name'],
        'students_enrolled' => $studentCount
    ];
}

$enrolledQuery = "
    SELECT s.firstName, s.lastName, s.enrolment_number, s.created_at, c.course_name
    FROM students s
    JOIN user_courses uc ON s.id = uc.user_id
    JOIN courses c ON uc.course_id = c.id
    ORDER BY s.created_at DESC
    LIMIT 10";  // Fetch the most recent 10 enrollments

$enrolledResult = $conn->query($enrolledQuery);

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
  <title>Pharmers Admin</title>
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
                  <h3 class="font-weight-bold">Welcome User</h3>
                  <h6 class="font-weight-normal mb-0">All systems are running smoothly!</h6>
                </div>
                <div class="col-12 col-xl-4">
                 <div class="justify-content-end d-flex">
                 <div class="flex-md-grow-1 flex-xl-grow-0">
    <button class="btn btn-sm btn-light bg-white">
        <i class="mdi mdi-calendar"></i> Today (<span id="currentDate"></span>)
    </button>
</div>
                 </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
         
            <div class="col-md-12 grid-margin transparent">
              <div class="row">
                <div class="col-md-6 mb-4 stretch-card transparent">
                  <div class="card card-tale">
                    <div class="card-body">
                      <p class="mb-4">Total No of Users</p>
                      <p class="fs-30 mb-2"> <?= $totalUsers; ?></p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                  <div class="card card-dark-blue">
                    <div class="card-body">
                      <p class="mb-4">Total Students</p>
                      <p class="fs-30 mb-2"> <?= $totalStudents; ?></p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                  <div class="card card-light-blue">
                    <div class="card-body">
                      <p class="mb-4">No of Instructors</p>
                      <p class="fs-30 mb-2"> <?= $totalInstructors; ?></p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 stretch-card transparent">
                  <div class="card card-light-danger">
                    <div class="card-body">
                      <p class="mb-4">No of Courses</p>
                      <p class="fs-30 mb-2"><?= $totalCourses; ?></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
       
        
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Courses Enrolment</p>
                  <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>Course</th>
                          <th>No of students</th>
                        </tr>  
                      </thead>
                      <tbody>
                      <?php foreach ($courses as $course): ?>
                        <tr>
                          <td><?= $course['name']; ?></td>
                          <td><?= $course['students_enrolled']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
        
          </div>
      
          <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <p class="card-title">Recently Enrolled Students</p>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="example" class="display expandable-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Admission Number</th>
                                        <th>Course Enrolled</th>
                                        <th>Enrollment Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enrolledStudents as $student): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($student['firstName'] . ' ' . $student['lastName']); ?></td>
                                            <td class="font-weight-bold"><?= htmlspecialchars($student['enrolment_number']); ?></td>
                                            <td><?= htmlspecialchars($student['course_name']); ?></td>
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
  <script>
    // Function to get today's date in "DD MMM YYYY" format
    function getFormattedDate() {
        const today = new Date();
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        return today.toLocaleDateString('en-GB', options);
    }

    // Set today's date in the span
    document.getElementById('currentDate').textContent = getFormattedDate();
</script>
  <script src="../script.js"></script>
</body>

</html>

