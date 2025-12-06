<?php
require_once '../backend/auth.php';
requireStudent();

$user = getCurrentUser();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $purpose = trim($_POST['purpose'] ?? '');
    $note = trim($_POST['note'] ?? '');

    if (empty($date) || empty($time) || empty($purpose)) {
        $message = 'Please fill in all required fields.';
    } elseif (strtotime($date) < strtotime('today')) {
        $message = 'Cannot book appointments for past dates.';
    } else {
        global $pdo;

        // Check if the time slot is already booked for this date
        $stmt = $pdo->prepare("SELECT id FROM appointments WHERE date = ? AND time = ? AND status IN ('pending', 'approved')");
        $stmt->execute([$date, $time]);
        if ($stmt->fetch()) {
            $message = 'This time slot is already booked. Please choose a different time.';
        } else {
            // Insert the appointment
            $stmt = $pdo->prepare("INSERT INTO appointments (student_id, date, time, purpose, note) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$user['id'], $date, $time, $purpose, $note])) {
                logActivity($user['id'], "Booked appointment for $date at $time");
                $message = 'Appointment booked successfully! It is pending approval.';
            } else {
                $message = 'Failed to book appointment. Please try again.';
            }
        }
    }
}

// Get available time slots
global $pdo;
$stmt = $pdo->query("SELECT time_label FROM time_slots WHERE is_available = 1 ORDER BY time_label");
$timeSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Student Appointment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../frontend/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Book New Appointment</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-info"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="" onsubmit="return validateForm('bookingForm')">
                            <div class="mb-3">
                                <label for="date" class="form-label">Preferred Date *</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="mb-3">
                                <label for="time" class="form-label">Preferred Time *</label>
                                <select class="form-control" id="time" name="time" required>
                                    <option value="">Select a time slot</option>
                                    <?php foreach ($timeSlots as $slot): ?>
                                        <option value="<?php echo htmlspecialchars($slot); ?>"><?php echo htmlspecialchars($slot); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="purpose" class="form-label">Purpose of Appointment *</label>
                                <select class="form-control" id="purpose" name="purpose" required>
                                    <option value="">Select purpose</option>
                                    <option value="Academic Advising">Academic Advising</option>
                                    <option value="Career Counseling">Career Counseling</option>
                                    <option value="Financial Aid">Financial Aid</option>
                                    <option value="Registration Issues">Registration Issues</option>
                                    <option value="Grade Concerns">Grade Concerns</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="note" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Any additional information..."></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Book Appointment</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
