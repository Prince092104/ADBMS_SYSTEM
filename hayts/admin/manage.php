<?php
require_once '../backend/auth.php';
requireAdmin();

$user = getCurrentUser();
$message = '';

global $pdo;

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $appointmentId = $_POST['appointment_id'] ?? '';

    if ($action && $appointmentId) {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'approved' WHERE id = ?");
            $stmt->execute([$appointmentId]);
            logActivity($user['id'], "Approved appointment ID: $appointmentId");
            $message = 'Appointment approved successfully.';
        } elseif ($action === 'decline') {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'declined' WHERE id = ?");
            $stmt->execute([$appointmentId]);
            logActivity($user['id'], "Declined appointment ID: $appointmentId");
            $message = 'Appointment declined successfully.';
        } elseif ($action === 'complete') {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?");
            $stmt->execute([$appointmentId]);
            logActivity($user['id'], "Marked appointment ID: $appointmentId as completed");
            $message = 'Appointment marked as completed.';
        } elseif ($action === 'cancel') {
            $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
            $stmt->execute([$appointmentId]);
            logActivity($user['id'], "Cancelled appointment ID: $appointmentId");
            $message = 'Appointment cancelled successfully.';
        }
    }
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';

// Build query
$query = "
    SELECT a.*, u.name as student_name, u.email as student_email
    FROM appointments a
    JOIN users u ON a.student_id = u.id
";
$conditions = [];
$params = [];

if ($statusFilter) {
    $conditions[] = "a.status = ?";
    $params[] = $statusFilter;
}

if ($dateFilter) {
    $conditions[] = "a.date = ?";
    $params[] = $dateFilter;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY a.date DESC, a.time DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

// Get appointment counts for filter tabs
$counts = [];
$statuses = ['pending', 'approved', 'declined', 'completed', 'cancelled'];
foreach ($statuses as $status) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointments WHERE status = ?");
    $stmt->execute([$status]);
    $counts[$status] = $stmt->fetch()['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h4 class="mb-4">Admin Dashboard</h4>
            <nav class="nav flex-column">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a class="nav-link active" href="manage.php">
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
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h2 class="mb-4">Manage Appointments</h2>

            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="declined" <?php echo $statusFilter === 'declined' ? 'selected' : ''; ?>>Declined</option>
                                <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?php echo $dateFilter; ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="manage.php" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Status Tabs -->
            <div class="mb-4">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link <?php echo !$statusFilter ? 'active' : ''; ?>" href="manage.php">All (<?php echo array_sum($counts); ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>" href="manage.php?status=pending">Pending (<?php echo $counts['pending']; ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $statusFilter === 'approved' ? 'active' : ''; ?>" href="manage.php?status=approved">Approved (<?php echo $counts['approved']; ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $statusFilter === 'declined' ? 'active' : ''; ?>" href="manage.php?status=declined">Declined (<?php echo $counts['declined']; ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $statusFilter === 'completed' ? 'active' : ''; ?>" href="manage.php?status=completed">Completed (<?php echo $counts['completed']; ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $statusFilter === 'cancelled' ? 'active' : ''; ?>" href="manage.php?status=cancelled">Cancelled (<?php echo $counts['cancelled']; ?>)</a>
                    </li>
                </ul>
            </div>

            <!-- Appointments Table -->
            <div class="card">
                <div class="card-header">
                    <h5>Appointments</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($appointments)): ?>
                        <p class="text-muted">No appointments found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Email</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Purpose</th>
                                        <th>Note</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($appointment['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['student_email']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($appointment['date'])); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['time']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['purpose']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['note'] ?? ''); ?></td>
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
                                                <div class="btn-group" role="group">
                                                    <?php if ($appointment['status'] === 'pending'): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-success" onclick="return confirm('Approve this appointment?')">Approve</button>
                                                        </form>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                            <button type="submit" name="action" value="decline" class="btn btn-sm btn-danger" onclick="return confirm('Decline this appointment?')">Decline</button>
                                                        </form>
                                                    <?php elseif ($appointment['status'] === 'approved'): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                            <button type="submit" name="action" value="complete" class="btn btn-sm btn-primary" onclick="return confirm('Mark as completed?')">Complete</button>
                                                        </form>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                            <button type="submit" name="action" value="cancel" class="btn btn-sm btn-warning" onclick="return confirm('Cancel this appointment?')">Cancel</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
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
