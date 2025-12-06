<?php
require_once '../backend/auth.php';
$user = getCurrentUser();
$userType = $user['user_type'] ?? '';
?>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/hayts/index.php"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/hayts/index.php">Home</a>
                </li>
                <?php if ($userType === 'student'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/hayts/student/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/hayts/student/book.php">Book Appointment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/hayts/student/history.php">Appointment History</a>
                    </li>
                <?php elseif ($userType === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/hayts/admin/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/hayts/admin/manage.php">Manage Appointments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/hayts/admin/slots.php">Manage Time Slots</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/hayts/admin/reports.php">View Reports</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        Welcome, <?php echo htmlspecialchars($user['name']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/hayts/logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
