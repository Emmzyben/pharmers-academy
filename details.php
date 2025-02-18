<?php
include 'backend/database/db_config.php'; 

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $course_id = intval($_GET['id']);

    // Fetch course details
    $query = "SELECT id, course_name, price, description FROM courses WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $course = $result->fetch_assoc();
    } else {
        echo "<script>alert('Course not found!'); window.location.href='index.php';</script>";
        exit;
    }

    // Fetch modules associated with this course
    $module_query = "SELECT id, module_title, module_description FROM modules WHERE course_id = ?";
    $module_stmt = $conn->prepare($module_query);
    $module_stmt->bind_param("i", $course_id);
    $module_stmt->execute();
    $modules_result = $module_stmt->get_result();

} else {
    echo "<script>alert('Invalid course ID!'); window.location.href='index.php';</script>";
    exit;
}

$query = "SELECT id, course_name, price FROM courses ORDER BY RAND() LIMIT 6";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Courses</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
   <link href="img/logo.jpg" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
           <img src="img/logo.jpg" alt="" width="170px" height="70px">
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link">Home</a>
                <a href="about.php" class="nav-item nav-link">About</a>
                <a href="courses.php" class="nav-item nav-link">Courses</a>
 
                <a href="contact.php" class="nav-item nav-link">Contact</a>
            </div>
            <a href="backend/login.php" class="btn btn-primary py-4 px-lg-5 d-lg-block">Get Started<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>
    <!-- Navbar End -->


    <!-- Header Start -->
   
    <!-- Header End -->



    <!-- Courses Start -->
    <div class="container" style="margin-top: 60px;">
        <h2 class="text-center"><?php echo htmlspecialchars($course['course_name']); ?></h2>
        <p class="price"><strong>Price:</strong> RS<?php echo number_format($course['price'], 2); ?></p>
        
        <div class="course-description">
            <h5>Description:</h5>
            <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
        </div>

        <hr>

        <!-- Display Modules -->
        <div class="course-modules">
            <h5>Course Modules</h5>
            <?php if ($modules_result->num_rows > 0): ?>
                <ul>
                    <?php while ($module = $modules_result->fetch_assoc()): ?>
                        <li>
                            <h6><?php echo htmlspecialchars($module['module_title']); ?></h6>
                            <p><?php echo nl2br(htmlspecialchars($module['module_description'])); ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No modules found for this course.</p>
            <?php endif; ?>
        </div>

        <hr>

        <div class="actions">
            <a href="backend/register.php?id=<?php echo $course['id']; ?>" class="btn btn-primary">Enroll Now</a>
            <a href="courses.php" class="btn btn-secondary">All Courses</a>
        </div>
    </div>

    <!-- Courses End -->
    <div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">Courses</h6>
            <h1 class="mb-5">More Popular Courses</h1>
        </div>
        <div class="row g-4 justify-content-center">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col-lg-4 col-md-6 wow fadeInUp course-item bg-light" data-wow-delay="0.1s" style="margin: 2px;">
                    <div class="text-center p-4 pb-0">
                        <h3 class="mb-0">RS<?php echo number_format($row['price'], 2); ?></h3>
                        <div class="mb-3">
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small class="fa fa-star text-primary"></small>
                            <small>(123)</small>
                        </div>
                        <div style="padding: 20px;">
                            <h5 class="mb-4"><?php echo htmlspecialchars($row['course_name']); ?></h5>   
                        </div>
                        <div class="course-item bg-light">
                            <div class="overflow-hidden">
                                <div class="w-100 d-flex justify-content-center bottom-0 start-0 mb-4">
                                    <a href="details.php?id=<?php echo $row['id']; ?>" class="flex-shrink-0 btn btn-sm btn-primary px-3 border-end" style="border-radius: 30px 0 0 30px;">Read More</a>
                                    <a href="backend/register.php?id=<?php echo $row['id']; ?>" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">Get Started</a>
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

   
        

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Quick Link</h4>
                    <a class="btn btn-link" href="">About Us</a>
                    <a class="btn btn-link" href="">Contact Us</a>
                     
                    <a class="btn btn-link" href="">Terms & Condition</a>
                    <a class="btn btn-link" href="">FAQs & Help</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Contact</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Centurion, Gauteng</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>010 222 0118</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>academy@pharmers.co.za</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
         
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-white mb-3">Newsletter</h4>
                    <p>Enter your email to subscribe</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">Pharmers Academy 2025</a>, All Right Reserved.

                        <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                        Designed By <a class="border-bottom" href="https://htmlcodex.com">Argon tech</a><br>
                    </div>
                 
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>