<?php
// Use SQLite database
$dbFile = __DIR__ . '/../db/surat_masama.db';

// Create database directory if needed
if (!file_exists(dirname($dbFile))) {
    mkdir(dirname($dbFile), 0755, true);
}

try {
    // Connect to SQLite database
    $conn = new PDO("sqlite:$dbFile");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS surat_masuk (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nomor_surat TEXT NOT NULL UNIQUE,
            tanggal TEXT NOT NULL,
            perihal TEXT NOT NULL,
            pengirim TEXT NOT NULL,
            ditujukan TEXT NOT NULL,
            isi TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS surat_keluar (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nomor_surat TEXT NOT NULL UNIQUE,
            tanggal TEXT NOT NULL,
            perihal TEXT NOT NULL,
            ditujukan TEXT NOT NULL,
            isi TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        INSERT OR IGNORE INTO users (username, password_hash) 
        VALUES ('admin', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
    ");
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function formatTanggal($date) {
    return date('d/m/Y', strtotime($date));
}
?>
