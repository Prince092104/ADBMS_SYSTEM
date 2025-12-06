<?php
require_once 'backend/auth.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = 'Please fill in all fields.';
    } elseif ($password !== $confirmPassword) {
        $message = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } elseif (registerUser($name, $email, $password)) {
        $message = 'Registration successful! You can now <a href="login.php">login</a>.';
    } else {
        $message = 'Registration failed. Email may already be in use.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Student Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <h4 class="mb-4">Create Your Account</h4>
                        <?php if ($message): ?>
                            <div class="alert alert-info"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                                    <label for="name">Full Name</label>
                                </div>
                            </div>
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                                    <label for="email">Email Address</label>
                                </div>
                            </div>
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required minlength="6">
                                    <label for="password">Password</label>
                                </div>
                            </div>
                            <div class="mb-4 input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required minlength="6">
                                    <label for="confirm_password">Confirm Password</label>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">Register</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
                            <p class="mb-0"><a href="index.php" class="text-decoration-none">Back to Home</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
