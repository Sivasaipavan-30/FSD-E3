-- Database: campus_event_db
CREATE DATABASE IF NOT EXISTS campus_event_db;
USE campus_event_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events Table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    department VARCHAR(100) NOT NULL,
    datetime DATETIME NOT NULL,
    venue VARCHAR(150) NOT NULL,
    price DECIMAL(10, 2) DEFAULT 0.00,
    seats INT NOT NULL,
    available_seats INT NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    tickets INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Insert Default Admin (Password: admin123)
-- Note: In production, always use password_hash()
INSERT INTO users (name, email, password, role) 
VALUES ('System Admin', 'admin@campus.com', '$2y$10$bF3EKOX24Mw91XSBkiUlpO2FCM7BZV.Kg40u4.JPWSp4LeNmwzKWC', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- Add Sample Events
INSERT INTO events (name, department, datetime, venue, price, seats, available_seats, description)
VALUES 
('Tech Nexus 2026', 'Computer Science', '2026-05-15 10:00:00', 'Main Auditorium', 5.00, 200, 200, 'The biggest technical festival of the campus featuring coding battles and AI workshops.'),
('Cultural Fusion Night', 'Performing Arts', '2026-06-10 18:30:00', 'Campus Open Ground', 0.00, 500, 500, 'A night of music, dance, and drama celebrating the diversity of our campus.'),
('AI & Robotics Summit', 'Computer Science', '2026-07-20 09:00:00', 'Tech Hall A', 15.00, 100, 100, 'A deep dive into advancements in AI, machine learning, and robotics with industry experts.'),
('Spring Rhythms Festival', 'Performing Arts', '2026-05-30 17:00:00', 'Central Lawn', 0.00, 800, 800, 'The annual campus music and dance festival featuring student performances and local bands.'),
('InnovateX Hackathon', 'Engineering', '2026-08-12 10:00:00', 'Innovation Lab', 10.00, 150, 150, 'A 24-hour coding challenge to solve real-world campus problems. Prize pool of ₹50,000!'),
('Global Career Fair 2026', 'Placement Cell', '2026-09-05 09:30:00', 'Main Plaza', 0.00, 1000, 1000, 'Connect with top-tier companies and explore internship and job opportunities across all sectors.'),
('Inter-Campus Sports Meet', 'Physical Education', '2026-10-15 08:00:00', 'Athletic Stadium', 2.00, 2000, 2000, 'A massive sporting event featuring football, athletics, and basketball with 10 neighboring colleges.');

-- Booking Attendees Table
CREATE TABLE IF NOT EXISTS booking_attendees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    vtu_no VARCHAR(100) NOT NULL,
    reg_no VARCHAR(100) NOT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);
