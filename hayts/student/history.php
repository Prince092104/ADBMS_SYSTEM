<?php
require_once '../backend/auth.php';
requireStudent();

$user = getCurrentUser();

// Get appointment history
global $pdo;
$stmt = $pdo->prepare("
    SELECT id, date, time, purpose, note, status, created_at
    FROM appointments
    WHERE student_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user['id']]);
$appointments = $stmt->fetchAll();

// Separate upcoming and past appointments
$upcoming = [];
$past = [];
$currentDate = date('Y-m-d');

foreach ($appointments as $appointment) {
    if ($appointment['date'] >= $currentDate && in_array($appointment['status'], ['pending', 'approved'])) {
        $upcoming[] = $appointment;
    } else {
        $past[] = $appointment;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment History - Student Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../frontend/navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">My Appointment History</h2>

        <!-- Upcoming Appointments -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5>Upcoming Appointments</h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcoming)): ?>
                    <p class="text-muted">No upcoming appointments.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming as $appointment): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($appointment['date'])); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['time']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['purpose']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                echo $appointment['status'] === 'approved' ? 'success' : 'warning';
                                            ?>">
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewDetails(<?php echo $appointment['id']; ?>)">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Past Appointments -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5>Past Appointments</h5>
            </div>
            <div class="card-body">
                <?php if (empty($past)): ?>
                    <p class="text-muted">No past appointments.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($past as $appointment): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($appointment['date'])); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['time']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['purpose']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                switch ($appointment['status']) {
                                                    case 'completed': echo 'success'; break;
                                                    case 'cancelled': echo 'danger'; break;
                                                    case 'declined': echo 'danger'; break;
                                                    default: echo 'secondary';
                                                }
                                            ?>">
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewDetails(<?php echo $appointment['id']; ?>)">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            <a href="book.php" class="btn btn-success">Book New Appointment</a>
        </div>
    </div>

    <!-- Modal for appointment details -->
    <div class="modal fade" id="appointmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="appointmentDetails">
                    <!-- Details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        function viewDetails(appointmentId) {
            // Load appointment details via AJAX
            fetch(`../backend/get_appointment.php?id=${appointmentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const appointment = data.appointment;
                        document.getElementById('appointmentDetails').innerHTML = `
                            <p><strong>Date:</strong> ${new Date(appointment.date).toLocaleDateString()}</p>
                            <p><strong>Time:</strong> ${appointment.time}</p>
                            <p><strong>Purpose:</strong> ${appointment.purpose}</p>
                            <p><strong>Status:</strong> ${appointment.status}</p>
                            <p><strong>Notes:</strong> ${appointment.note || 'None'}</p>
                            <p><strong>Created:</strong> ${new Date(appointment.created_at).toLocaleString()}</p>
                        `;
                        new bootstrap.Modal(document.getElementById('appointmentModal')).show();
                    } else {
                        alert('Failed to load appointment details.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while loading details.');
                });
        }
    </script>
</body>
</html>
