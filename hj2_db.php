<?php
// Foutrapportage aan tijdens het bouwen zodat we alles zien
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // 🔥 NIEUW EN ONAFHANKELIJK DATABASESCHIP
    $db_path = '/var/www/html/HitData/hitjam2.db';
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. TABEL: Gebruikers & Wachtwoorden
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'speler',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. TABEL: De Muziekbibliotheek
    $db->exec("CREATE TABLE IF NOT EXISTS game_songs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        artist TEXT NOT NULL,
        title TEXT NOT NULL,
        year INTEGER NOT NULL,
        theme TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. TABEL: Live Multiplayersynchronisatie
    $db->exec("CREATE TABLE IF NOT EXISTS game_status (
        id INTEGER PRIMARY KEY,
        current_song_id INTEGER DEFAULT 0,
        round_active INTEGER DEFAULT 0,
        music_started INTEGER DEFAULT 0,
        start_time REAL DEFAULT 0,
        gestart_door TEXT DEFAULT ''
    )");

    // 4. TABEL: Live Multiplayer Scorebord & Antwoorden [INDEX]
    $db->exec("CREATE TABLE IF NOT EXISTS scores (
        username TEXT PRIMARY KEY,
        points INTEGER DEFAULT 0,
        gekozen_jaar INTEGER DEFAULT 0
    )");

    // Zorg voor de standaard statusrij (id=1)
    $checkStatus = $db->query("SELECT COUNT(*) FROM game_status")->fetchColumn();
    if ($checkStatus == 0) {
        $db->exec("INSERT INTO game_status (id, current_song_id, round_active, music_started) VALUES (1, 0, 0, 0)");
    }

    // Zorg voor het standaard admin-account (admin / admin123)
    $checkAdmin = $db->query("SELECT COUNT(*) FROM users WHERE username = 'admin'")->fetchColumn();
    if ($checkAdmin == 0) {
        $hashed_password = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
        $stmt->execute(['admin', $hashed_password]);
    }

} catch (Exception $e) {
    die("<div style='color:red; font-family:sans-serif; padding:20px;'>❌ Database startfout: " . $e->getMessage() . "</div>");
}
?>
