<?php
require_once '../backend/auth.php';
$user = getCurrentUser();
if ($user['role'] !== 'student' && $user['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$user = getCurrentUser();

// Get upcoming appointments
global $pdo;
$currentDate = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT id, date, time, purpose, status
    FROM appointments
    WHERE student_id = ? AND date >= ? AND status IN ('pending', 'approved')
    ORDER BY date ASC, time ASC
    LIMIT 5
");
$stmt->execute([$user['id'], $currentDate]);
$upcomingAppointments = $stmt->fetchAll();

// Get appointment statistics
$stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM appointments WHERE student_id = ? GROUP BY status");
$stmt->execute([$user['id']]);
$stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Student Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h4 class="mb-4">Student Dashboard</h4>
            <nav class="nav flex-column">
                <a class="nav-link active" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a class="nav-link" href="book.php">
                    <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                </a>
                <a class="nav-link" href="history.php">
                    <i class="fas fa-history me-2"></i>Appointment History
                </a>
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>

            <!-- Status Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card status-card pending">
                        <div class="card-body text-center">
                            <h4><?php echo $stats['pending'] ?? 0; ?></h4>
                            <p class="mb-0">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card status-card approved">
                        <div class="card-body text-center">
                            <h4><?php echo $stats['approved'] ?? 0; ?></h4>
                            <p class="mb-0">Approved</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card status-card completed">
                        <div class="card-body text-center">
                            <h4><?php echo $stats['completed'] ?? 0; ?></h4>
                            <p class="mb-0">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card status-card cancelled">
                        <div class="card-body text-center">
                            <h4><?php echo ($stats['declined'] ?? 0) + ($stats['cancelled'] ?? 0); ?></h4>
                            <p class="mb-0">Cancelled</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="book.php" class="btn btn-success btn-lg w-100 mb-2">
                                Book New Appointment
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="history.php" class="btn btn-info btn-lg w-100 mb-2">
                                View Appointment History
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="card">
                <div class="card-header">
                    <h5>Upcoming Appointments</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($upcomingAppointments)): ?>
                        <p class="text-muted">No upcoming appointments. <a href="book.php">Book one now</a>.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($upcomingAppointments as $appointment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($appointment['purpose']); ?></h6>
                                        <small><?php echo date('M d, Y', strtotime($appointment['date'])); ?> at <?php echo htmlspecialchars($appointment['time']); ?></small>
                                    </div>
                                    <p class="mb-1">Status:
                                        <span class="badge bg-<?php echo $appointment['status'] === 'approved' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-3">
                            <a href="history.php" class="btn btn-outline-primary">View All Appointments</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
