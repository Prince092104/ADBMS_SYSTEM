<?php
require_once '../backend/auth.php';
requireAdmin();

// Get appointment status counts
global $pdo;
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM appointments GROUP BY status ORDER BY count DESC");
$statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user role counts
$stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role ORDER BY count DESC");
$roleCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get most common appointment purposes
$stmt = $pdo->query("SELECT purpose, COUNT(*) as count FROM appointments GROUP BY purpose ORDER BY count DESC LIMIT 10");
$purposeCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get daily appointments for last 30 days
$stmt = $pdo->query("
    SELECT DATE(date) as date, COUNT(*) as count
    FROM appointments
    WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(date)
    ORDER BY date DESC
");
$dailyCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get weekly appointments for last 12 weeks
$stmt = $pdo->query("
    SELECT YEARWEEK(date) as week, COUNT(*) as count
    FROM appointments
    WHERE date >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK)
    GROUP BY YEARWEEK(date)
    ORDER BY week DESC
");
$weeklyCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Student Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../frontend/navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Appointment Reports</h2>

        <div class="row">
            <!-- Appointment Statuses -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Appointment Statuses</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($statusCounts as $status): ?>
                                        <tr>
                                            <td><?php echo ucfirst(htmlspecialchars($status['status'])); ?></td>
                                            <td><?php echo $status['count']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Roles -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>User Roles</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Role</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roleCounts as $role): ?>
                                        <tr>
                                            <td><?php echo ucfirst(htmlspecialchars($role['role'])); ?></td>
                                            <td><?php echo $role['count']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Common Purposes -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Most Common Appointment Purposes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Purpose</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purposeCounts as $purpose): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($purpose['purpose']); ?></td>
                                    <td><?php echo $purpose['count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Daily Appointments (Last 30 Days) -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Daily Appointments (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dailyCounts as $day): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($day['date'])); ?></td>
                                            <td><?php echo $day['count']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Appointments (Last 12 Weeks) -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Weekly Appointments (Last 12 Weeks)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Week</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($weeklyCounts as $week): ?>
                                        <tr>
                                            <td><?php echo $week['week']; ?></td>
                                            <td><?php echo $week['count']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            <button onclick="window.print()" class="btn btn-secondary">Print Report</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
