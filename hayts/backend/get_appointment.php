<?php
require_once 'auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$appointmentId = $_GET['id'] ?? 0;

if (!$appointmentId) {
    echo json_encode(['success' => false, 'message' => 'Appointment ID required']);
    exit();
}

global $pdo;

// Check if user can view this appointment
$user = getCurrentUser();
$whereClause = '';
$params = [$appointmentId];

if (isStudent()) {
    $whereClause = 'AND a.student_id = ?';
    $params[] = $user['id'];
}

$query = "
    SELECT a.*, u.name as student_name, u.email as student_email
    FROM appointments a
    JOIN users u ON a.student_id = u.id
    WHERE a.id = ? $whereClause
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$appointment = $stmt->fetch();

if ($appointment) {
    echo json_encode([
        'success' => true,
        'appointment' => $appointment
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Appointment not found or access denied'
    ]);
}
?>
