<?php
session_start();
require_once 'db.php';

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to check if user is student
function isStudent() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

// Function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Function to require admin access
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: index.php');
        exit();
    }
}

// Function to require student access
function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        header('Location: index.php');
        exit();
    }
}

// Function to login user
function loginUser($email, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Log the login activity
        logActivity($user['id'], 'User logged in');

        return true;
    }

    return false;
}

// Function to register user
function registerUser($name, $email, $password, $role = 'student') {
    global $pdo;

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return false; // Email already exists
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$name, $email, $hashedPassword, $role]);

    if ($result) {
        $userId = $pdo->lastInsertId();
        logActivity($userId, 'User registered');
    }

    return $result;
}

// Function to logout user
function logoutUser() {
    if (isLoggedIn()) {
        logActivity($_SESSION['user_id'], 'User logged out');
    }

    session_destroy();
    header('Location: index.php');
    exit();
}

// Function to log activity
function logActivity($userId, $activity) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("INSERT INTO logs (user_id, activity) VALUES (?, ?)");
        $stmt->execute([$userId, $activity]);
    } catch (Exception $e) {
        // Log to file if database logging fails
        error_log("Activity log failed: " . $e->getMessage());
    }
}

// Function to get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['role']
    ];
}
?>
