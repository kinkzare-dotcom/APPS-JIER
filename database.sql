-- Database: pss_jier
CREATE DATABASE IF NOT EXISTS pss_jier;
USE pss_jier;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id_user INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- MD5 Hash
    role ENUM('admin', 'siswa') NOT NULL DEFAULT 'siswa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Kategori Table
CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
);

-- Aspirasi (Complaints) Table
CREATE TABLE IF NOT EXISTS aspirasi (
    id_aspirasi INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_user INT(11) NOT NULL,
    id_kategori INT(11) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Menunggu', 'Proses', 'Selesai') DEFAULT 'Menunggu',
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE CASCADE
);

-- Umpan Balik (Feedback) Table
CREATE TABLE IF NOT EXISTS umpan_balik (
    id_feedback INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_aspirasi INT(11) NOT NULL,
    id_user INT(11) NOT NULL, -- Admin who replied
    respon TEXT NOT NULL,
    tanggal_respon DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_aspirasi) REFERENCES aspirasi(id_aspirasi) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
);

-- Seed Initial Data
INSERT INTO users (nama, username, password, role) VALUES 
('Administrator', 'admin', MD5('admin123'), 'admin'),
('Siswa Contoh', 'siswa', MD5('siswa123'), 'siswa');

INSERT INTO kategori (nama_kategori) VALUES 
('Sarana (Fasilitas)'), 
('Prasarana (Bangunan)'), 
('Kebersihan'), 
('Keamanan'),
('Lainnya');
