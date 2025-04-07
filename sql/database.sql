-- Database setup for Surat Masuk/Keluar System
CREATE DATABASE IF NOT EXISTS surat_masama;
USE surat_masama;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Incoming letters table
CREATE TABLE surat_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(20) NOT NULL UNIQUE,
    tanggal DATE NOT NULL,
    perihal VARCHAR(255) NOT NULL,
    pengirim VARCHAR(100) NOT NULL,
    ditujukan VARCHAR(100) NOT NULL,
    isi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Outgoing letters table
CREATE TABLE surat_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(20) NOT NULL UNIQUE,
    tanggal DATE NOT NULL,
    perihal VARCHAR(255) NOT NULL,
    ditujukan VARCHAR(100) NOT NULL,
    isi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: masama2023)
INSERT INTO users (username, password_hash) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');