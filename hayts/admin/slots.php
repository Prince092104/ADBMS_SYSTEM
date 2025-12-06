<?php
require_once '../backend/auth.php';
requireAdmin();

$user = getCurrentUser();
$message = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $slotId = $_POST['slot_id'] ?? 0;
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;

        global $pdo;
        $stmt = $pdo->prepare("UPDATE time_slots SET is_available = ? WHERE id = ?");
        if ($stmt->execute([$isAvailable, $slotId])) {
            logActivity($user['id'], "Updated time slot ID: $slotId availability to " . ($isAvailable ? 'available' : 'unavailable'));
            $message = 'Time slot updated successfully.';
        } else {
            $message = 'Failed to update time slot.';
        }
    } elseif ($action === 'add') {
        $timeLabel = trim($_POST['time_label'] ?? '');

        if (empty($timeLabel)) {
            $message = 'Please enter a time label.';
        } else {
            global $pdo;
            $stmt = $pdo->prepare("INSERT INTO time_slots (time_label, is_available) VALUES (?, 1)");
            if ($stmt->execute([$timeLabel])) {
                logActivity($user['id'], "Added new time slot: $timeLabel");
                $message = 'Time slot added successfully.';
            } else {
                $message = 'Failed to add time slot.';
            }
        }
    } elseif ($action === 'delete') {
        $slotId = $_POST['slot_id'] ?? 0;

        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM time_slots WHERE id = ?");
        if ($stmt->execute([$slotId])) {
            logActivity($user['id'], "Deleted time slot ID: $slotId");
            $message = 'Time slot deleted successfully.';
        } else {
            $message = 'Failed to delete time slot.';
        }
    }
}

// Get all time slots
global $pdo;
$stmt = $pdo->query("SELECT * FROM time_slots ORDER BY time_label");
$timeSlots = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Time Slots - Student Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../frontend/navbar.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Manage Time Slots</h2>

        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Add New Time Slot -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Add New Time Slot</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <input type="hidden" name="action" value="add">
                    <div class="col-md-8">
                        <label for="time_label" class="form-label">Time Label (e.g., "9:00 AM - 10:00 AM")</label>
                        <input type="text" class="form-control" id="time_label" name="time_label" required
                               placeholder="9:00 AM - 10:00 AM">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success">Add Time Slot</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Time Slots Table -->
        <div class="card">
            <div class="card-header">
                <h5>Available Time Slots (<?php echo count($timeSlots); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($timeSlots)): ?>
                    <p class="text-muted">No time slots configured.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Time Label</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($timeSlots as $slot): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($slot['time_label']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $slot['is_available'] ? 'success' : 'danger'; ?>">
                                                <?php echo $slot['is_available'] ? 'Available' : 'Unavailable'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="slot_id" value="<?php echo $slot['id']; ?>">
                                                    <input type="hidden" name="is_available" value="<?php echo $slot['is_available'] ? '0' : '1'; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-<?php echo $slot['is_available'] ? 'danger' : 'success'; ?>">
                                                        Make <?php echo $slot['is_available'] ? 'Unavailable' : 'Available'; ?>
                                                    </button>
                                                </form>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSlot(<?php echo $slot['id']; ?>, '<?php echo htmlspecialchars($slot['time_label']); ?>')">
                                                    Delete
                                                </button>
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

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        function deleteSlot(slotId, timeLabel) {
            if (confirm(`Are you sure you want to delete the time slot "${timeLabel}"? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="slot_id" value="${slotId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
