CREATE DATABASE IF NOT EXISTS db_peminjaman_soundbest
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_peminjaman_soundbest;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'karyawan', 'teknisi') NOT NULL DEFAULT 'karyawan'
);

CREATE TABLE IF NOT EXISTS barang (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(50) NOT NULL UNIQUE,
    nama_barang VARCHAR(150) NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    jumlah INT UNSIGNED NOT NULL DEFAULT 0,
    kondisi VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS peminjaman (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    barang_id INT UNSIGNED NOT NULL,
    jumlah INT UNSIGNED NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE NOT NULL,
    keperluan VARCHAR(255) NOT NULL DEFAULT '',
    status VARCHAR(50) NOT NULL DEFAULT 'Menunggu',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (nama, username, password, role)
SELECT 'Administrator', 'admin', MD5('admin123'), 'admin'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE username = 'admin'
);

INSERT INTO users (nama, username, password, role)
SELECT 'Karyawan Soundbest', 'karyawan', MD5('karyawan123'), 'karyawan'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE username = 'karyawan'
);

INSERT INTO users (nama, username, password, role)
SELECT 'Teknisi Soundbest', 'teknisi', MD5('teknisi123'), 'teknisi'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE username = 'teknisi'
);

INSERT IGNORE INTO barang (kode_barang, nama_barang, kategori, jumlah, kondisi) VALUES
    ('SND-001', 'Mixer Audio 16 Channel', 'Mixer', 2, 'Baik'),
    ('SND-002', 'Speaker Aktif 15 Inch', 'Speaker', 4, 'Baik'),
    ('SND-003', 'Microphone Wireless', 'Microphone', 6, 'Baik'),
    ('SND-004', 'Tripod Speaker', 'Aksesoris', 4, 'Baik'),
    ('SND-005', 'Kabel XLR 10 Meter', 'Kabel', 10, 'Baik');