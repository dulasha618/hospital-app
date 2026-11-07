-- Run this in phpMyAdmin: select "hospital_db" -> SQL tab -> paste -> Go
-- The users table (registered accounts) is KEPT if it already exists.
-- appointments, prescriptions, bills and medicines are re-created (old data in them is removed).

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullName VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Doctor', 'Patient', 'Receptionist') NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS bills;
DROP TABLE IF EXISTS medicines;

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patientId INT NOT NULL,
    doctorId INT NOT NULL,
    `date` DATE NOT NULL,
    `time` TIME NOT NULL,
    reason VARCHAR(255) NULL,
    status ENUM('Scheduled', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patientId INT NOT NULL,
    doctorId INT NOT NULL,
    diagnosis TEXT NOT NULL,
    medicines TEXT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patientId INT NOT NULL,
    consultationFee DECIMAL(10,2) NOT NULL DEFAULT 0,
    medicineFee DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('Unpaid', 'Paid') NOT NULL DEFAULT 'Unpaid',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    stock INT NOT NULL DEFAULT 0
);

INSERT INTO medicines (name, stock) VALUES
('Paracetamol 500mg', 120),
('Amoxicillin 250mg', 45),
('Cetirizine 10mg', 8),
('Omeprazole 20mg', 60),
('Salbutamol Inhaler', 5);
