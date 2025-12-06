<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://via.placeholder.com/40x40/FFFFFF/1E3A8A?text=S" alt="School Logo" class="me-2" style="border-radius: 50%;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#about-school">About School</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about-system">About System</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#developers">Developers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="btn btn-outline-light me-2" href="/hayts/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light" href="/hayts/register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Online Student Appointment System</h1>
                    <p class="lead">Book your appointments quickly and conveniently from anywhere, anytime.</p>
                    <a href="/hayts/login.php" class="btn btn-primary btn-lg">Get Started</a>
                </div>
                <div class="col-lg-6">
                    <img src="fbc-campus.png" alt="School Campus" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- About School Section -->
    <section id="about-school" class="section">
        <div class="container">
            <h2>About Our School</h2>
            <div class="row">
                <div class="col-lg-6">
                    <h4>Vision</h4>
                    <p>To be a leading educational institution that nurtures innovation, excellence, and character in every student.</p>
                    <h4>Mission</h4>
                    <p>Our mission is to provide quality education, foster critical thinking, and prepare students for successful careers and meaningful lives.</p>
                    <h4>History</h4>
                    <p>Founded in 1990, our school has been serving the community for over three decades, building a legacy of academic excellence and holistic development.</p>
                </div>
                <div class="col-lg-6">
                    <img src="frontend/schoolbldg.jpg" alt="School Building" class="img-fluid rounded" style="height: 300px;">
                </div>
            </div>
        </div>
    </section>

    <!-- About System Section -->
    <section id="about-system" class="section bg-light">
        <div class="container">
            <h2>About the System</h2>
            <div class="row">
                <div class="col-lg-6">
                    <p>Our Online Student Appointment System revolutionizes the way students schedule meetings with faculty and staff. Say goodbye to long queues and endless waiting times.</p>
                    <ul class="list-unstyled">
                        <li>✓ Easy online booking from any device</li>
                        <li>✓ Real-time availability updates</li>
                        <li>✓ No more waiting in lines</li>
                        <li>✓ Accessible 24/7</li>
                        <li>✓ Instant confirmation and reminders</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <img src="frontend/ECTDept.jpg" alt="Online System" class="img-fluid rounded" style="height: 300px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section">
        <div class="container">
            <h2>System Features</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Easy Booking</h5>
                            <p class="card-text">Simple and intuitive interface for booking appointments with just a few clicks.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Admin Approval</h5>
                            <p class="card-text">All appointments are reviewed and approved by administrators for better management.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Appointment Reminders</h5>
                            <p class="card-text">Get notified about upcoming appointments to never miss an important meeting.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Mobile Friendly</h5>
                            <p class="card-text">Fully responsive design works perfectly on desktops, tablets, and smartphones.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Real-time Updates</h5>
                            <p class="card-text">Live status updates and instant notifications keep you informed at all times.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Secure & Private</h5>
                            <p class="card-text">Your personal information and appointment details are kept secure and confidential.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Developers Section -->
    <section id="developers" class="section bg-light">
        <div class="container">
            <h2>Meet the Developers</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Prince Simon Villeza and Richard Ecle</h5>
                            <p class="card-text">Lead Developer<br>BSIT, 3rd Year</p>
                            <small class="text-muted">Backend & Database</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Via Escobedo, Faith Joy Cabiles</h5>
                            <p class="card-text">UI/UX Designer<br>Information Technology, 3rd Year</p>
                            <small class="text-muted">Frontend & Design</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">De Vera Rendell, Crisostomo Lance</h5>
                            <p class="card-text">Project Manager<br>Software Engineering, 4th Year</p>
                            <small class="text-muted">Testing & Documentation</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <div class="container">
            <h2>Contact Information</h2>
            <div class="row">
                <div class="col-md-6">
                    <h4>School Address</h4>
                    <p>KM 5 National Highway, San Jose, Puerto Princesa, Philippines, 5300</p>
                    <h4>Office Hours</h4>
                    <p>Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 9:00 AM - 1:00 PM</p>
                </div>
                <div class="col-md-6">
                    <h4>Get in Touch</h4>
                    <p><strong>Email:</strong> fullbrightcollege@yahoo.com</p>
                    <p><strong>Phone:</strong> 090064494442</p>
                    <p><strong>Fax:</strong> 09064494442</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Student Appointment System</h5>
                    <p>Making appointment booking simple and efficient for students and staff.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#about-school">About School</a></li>
                        <li><a href="#about-system">About System</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="/hayts/login.php">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <p>Stay connected with our social media channels for updates and announcements.</p>
                    <div>
                        <a href="#" class="text-white me-3">Facebook</a>
                        <a href="#" class="text-white me-3">Twitter</a>
                        <a href="#" class="text-white">Instagram</a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> Student Appointment System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
