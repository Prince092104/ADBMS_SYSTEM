-- Database: appointment_system
-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS appointment_system;
USE appointment_system;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Time slots table (optional for admin schedule)
CREATE TABLE IF NOT EXISTS time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    time_label VARCHAR(50) NOT NULL, -- e.g., '9:00 AM - 10:00 AM'
    is_available BOOLEAN DEFAULT TRUE
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    time VARCHAR(50) NOT NULL, -- or reference time_slots.id
    purpose TEXT NOT NULL,
    note TEXT,
    status ENUM('pending', 'approved', 'declined', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Logs table (optional)
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    activity TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123, hashed)
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@school.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample time slots
INSERT INTO time_slots (time_label, is_available) VALUES
('9:00 AM - 10:00 AM', TRUE),
('10:00 AM - 11:00 AM', TRUE),
('11:00 AM - 12:00 PM', TRUE),
('1:00 PM - 2:00 PM', TRUE),
('2:00 PM - 3:00 PM', TRUE),
('3:00 PM - 4:00 PM', TRUE);
