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
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Welcome User</h3>
                  <h6 class="font-weight-normal mb-0">All systems are running smoothly!</h6>
                </div>
                <div class="col-12 col-xl-4">
                 <div class="justify-content-end d-flex">
                  <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                     <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
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
                      <p class="fs-30 mb-2">4006</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                  <div class="card card-dark-blue">
                    <div class="card-body">
                      <p class="mb-4">Total Students</p>
                      <p class="fs-30 mb-2">3600</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                  <div class="card card-light-blue">
                    <div class="card-body">
                      <p class="mb-4">No of Instructors</p>
                      <p class="fs-30 mb-2">30</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 stretch-card transparent">
                  <div class="card card-light-danger">
                    <div class="card-body">
                      <p class="mb-4">No of Courses</p>
                      <p class="fs-30 mb-2">47</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
       
          <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Course information</p>
                  <div class="table-responsive">
                    <table class="table table-borderless">
                      <thead>
                        <tr>
                          <th class="pl-0  pb-2 border-bottom">Course</th>
                          <th class="border-bottom pb-2">Instructor</th>
                          <th class="border-bottom pb-2">No of students enrolled</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="pl-0">Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td><p class="mb-0">Emmanuel Amadi</p></td>
                          <td class="text-muted">65</td>
                        </tr>
                        <tr>
                          <td class="pl-0">Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td><p class="mb-0">Emmanuel Amadi</p></td>
                          <td class="text-muted">51</td>
                        </tr>
                        <tr>
                          <td class="pl-0">Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td><p class="mb-0">Emmanuel Amadi</p></td>
                          <td class="text-muted">32</td>
                        </tr>
                        <tr>
                          <td class="pl-0">Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td><p class="mb-0">Emmanuel Amadi</p></td>
                          <td class="text-muted">15</td>
                        </tr>
                        <tr>
                          <td class="pl-0">Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td><p class="mb-0">Emmanuel Amadi</p></td>
                          <td class="text-muted">25</td>
                        </tr>
                      
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
                  <p class="card-title mb-0">Top Courses</p>
                  <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                      <thead>
                        <tr>
                          <th>Course</th>
                          <th>Price</th>
                          <th>No of students</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <tr>
                          <td>Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td class="font-weight-bold">Rs362</td>
                          <td>20</td>
                        </tr>
                        <tr>
                          <td>Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td class="font-weight-bold">Rs362</td>
                          <td>20</td>
                        </tr>
                        <tr>
                          <td>Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td class="font-weight-bold">Rs362</td>
                          <td>20</td>
                        </tr>
                        <tr>
                          <td>Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td class="font-weight-bold">Rs362</td>
                          <td>20</td>
                        </tr>
                        <tr>
                          <td>Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td class="font-weight-bold">Rs362</td>
                          <td>20</td>
                        </tr>
                     
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
                  <p class="card-title">Recently enrolled students</p>
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                        <table id="example" class="display expandable-table" style="width:100%">
                          <thead>
                            <tr>
                              <th>Student name</th>
                              <th>Admission number</th>
                              <th>Course enrolled</th>
                              <th>Registration date</th>
                            </tr>
                          </thead>
                          <tbody>
                        <tr>
                          <td>Emmzy ben</td>
                          <td class="font-weight-bold">PHA-234567</td>
                          <td>Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td>21 Sep 2018</td>
                      </tr>
                      <tr>
                          <td>Emmzy ben</td>
                          <td class="font-weight-bold">PHA-234567</td>
                          <td>Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td>21 Sep 2018</td>
                      </tr>
                      <tr>
                          <td>Emmzy ben</td>
                          <td class="font-weight-bold">PHA-234567</td>
                          <td>Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td>21 Sep 2018</td>
                      </tr>
                      <tr>
                          <td>Emmzy ben</td>
                          <td class="font-weight-bold">PHA-234567</td>
                          <td>Masterclass 105 – Quality Assurance Fundamentals</td>
                          <td>21 Sep 2018</td>
                      </tr>
                      </tbody>
                      </table>
                      </div>
                    </div>
                  </div>
                  </div>
                </div>

                
              </div>
            </div>
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
</body>

</html>

