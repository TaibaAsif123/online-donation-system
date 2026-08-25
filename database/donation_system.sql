-- ============================================
-- Online Donation System - Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS donation_system;
USE donation_system;

-- Drop tables if they already exist (safe re-import)
DROP TABLE IF EXISTS donations;
DROP TABLE IF EXISTS donors;
DROP TABLE IF EXISTS causes;

-- ============================================
-- Table: causes
-- ============================================
CREATE TABLE causes (
    cause_id INT AUTO_INCREMENT PRIMARY KEY,
    cause_name VARCHAR(100) NOT NULL,
    description TEXT
);

-- ============================================
-- Table: donors
-- ============================================
CREATE TABLE donors (
    donor_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL
);

-- ============================================
-- Table: donations
-- ============================================
CREATE TABLE donations (
    donation_id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    cause_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    donation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_status VARCHAR(20) DEFAULT 'Pending',
    FOREIGN KEY (donor_id) REFERENCES donors(donor_id),
    FOREIGN KEY (cause_id) REFERENCES causes(cause_id)
);

-- ============================================
-- Seed data: causes (Member 1 needs these IDs)
-- ============================================
INSERT INTO causes (cause_name, description) VALUES
('Education', 'Support underprivileged children with school supplies, fees, and access to quality education.'),
('Health', 'Fund medical treatments, checkups, and medicine for people who cannot afford healthcare.'),
('Food', 'Provide meals and food supplies to families facing hunger and food insecurity.'),
('Emergency Relief', 'Help communities affected by natural disasters and emergencies with immediate aid.');

-- ============================================
-- (Optional) Sample donation data for testing
-- Uncomment if you want dummy data to test Member 4's report early
-- ============================================
-- INSERT INTO donors (name, email) VALUES ('Ali Raza', 'ali@example.com');
-- INSERT INTO donations (donor_id, cause_id, amount, payment_method, payment_status)
-- VALUES (1, 1, 500.00, 'Credit Card', 'Success');