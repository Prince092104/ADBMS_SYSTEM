<?php
require_once '../backend/auth.php';
requireAdmin();

$user = getCurrentUser();

// Get appointment statistics
global $pdo;

// Count pending appointments
$stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'");
$pendingCount = $stmt->fetch()['count'];

// Count approved appointments
$stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'approved'");
$approvedCount = $stmt->fetch()['count'];

// Count declined appointments
$stmt = $pdo->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'declined'");
$declinedCount = $stmt->fetch()['count'];

// Get today's appointments
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointments WHERE date = ?");
$stmt->execute([$today]);
$todayCount = $stmt->fetch()['count'];

// Get recent appointments
$stmt = $pdo->query("
    SELECT a.*, u.name as student_name
    FROM appointments a
    JOIN users u ON a.student_id = u.id
    ORDER BY a.created_at DESC
    LIMIT 10
");
$recentAppointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h4 class="mb-4">Admin Dashboard</h4>
            <nav class="nav flex-column">
                <a class="nav-link active" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a class="nav-link" href="manage.php">
                    <i class="fas fa-calendar-check me-2"></i>Manage Appointments
                </a>
                <a class="nav-link" href="slots.php">
                    <i class="fas fa-clock me-2"></i>Manage Time Slots
                </a>
                <a class="nav-link" href="reports.php">
                    <i class="fas fa-chart-bar me-2"></i>View Reports
                </a>
                <a class="nav-link" href="../student/dashboard.php">
                    <i class="fas fa-user-graduate me-2"></i>Student View
                </a>
                <a class="nav-link" href="/hayts/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2 class="mb-4">Admin Dashboard</h2>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card status-card pending">
                        <div class="card-body text-center">
                            <h4><?php echo $pendingCount; ?></h4>
                            <p class="mb-0">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card status-card approved">
                        <div class="card-body text-center">
                            <h4><?php echo $approvedCount; ?></h4>
                            <p class="mb-0">Approved</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card status-card cancelled">
                        <div class="card-body text-center">
                            <h4><?php echo $declinedCount; ?></h4>
                            <p class="mb-0">Declined</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card status-card completed">
                        <div class="card-body text-center">
                            <h4><?php echo $todayCount; ?></h4>
                            <p class="mb-0">Today</p>
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
                        <div class="col-md-3">
                            <a href="manage.php" class="btn btn-primary btn-lg w-100 mb-2">
                                Manage Appointments
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="slots.php" class="btn btn-secondary btn-lg w-100 mb-2">
                                Manage Time Slots
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="reports.php" class="btn btn-info btn-lg w-100 mb-2">
                                View Reports
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="../student/dashboard.php" class="btn btn-success btn-lg w-100 mb-2">
                                Student View
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="card">
                <div class="card-header">
                    <h5>Recent Appointments</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recentAppointments)): ?>
                        <p class="text-muted">No appointments found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentAppointments as $appointment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($appointment['student_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($appointment['date'])); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['time']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['purpose']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    switch ($appointment['status']) {
                                                        case 'pending': echo 'warning'; break;
                                                        case 'approved': echo 'success'; break;
                                                        case 'declined': echo 'danger'; break;
                                                        case 'completed': echo 'success'; break;
                                                        case 'cancelled': echo 'secondary'; break;
                                                        default: echo 'secondary';
                                                    }
                                                ?>">
                                                    <?php echo ucfirst($appointment['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="manage.php?action=view&id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
