<?php
// Database configuration
$host = 'localhost';
$dbname = 'appointment_system';
$username = 'root'; // Default XAMPP MySQL username
$password = ''; // Default XAMPP MySQL password (empty)

try {
    // First, connect without specifying the database to create it if it doesn't exist
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8 COLLATE utf8_general_ci");

    // Now connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create tables if they don't exist
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'student') DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS time_slots (
        id INT AUTO_INCREMENT PRIMARY KEY,
        time_label VARCHAR(50) NOT NULL,
        is_available BOOLEAN DEFAULT TRUE
    );

    CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        date DATE NOT NULL,
        time VARCHAR(50) NOT NULL,
        purpose TEXT NOT NULL,
        note TEXT,
        status ENUM('pending', 'approved', 'declined', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        activity TEXT NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    );
    ";

    $pdo->exec($sql);

    // Insert default admin user if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@school.edu']);
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin User', 'admin@school.edu', $hashedPassword, 'admin']);
    }

    // Insert default time slots if not exist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM time_slots");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $timeSlots = [
            '9:00 AM - 10:00 AM',
            '10:00 AM - 11:00 AM',
            '11:00 AM - 12:00 PM',
            '1:00 PM - 2:00 PM',
            '2:00 PM - 3:00 PM',
            '3:00 PM - 4:00 PM'
        ];
        $stmt = $pdo->prepare("INSERT INTO time_slots (time_label, is_available) VALUES (?, TRUE)");
        foreach ($timeSlots as $slot) {
            $stmt->execute([$slot]);
        }
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
