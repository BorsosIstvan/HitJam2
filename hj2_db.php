<?php
try {
    // We gebruiken dezelfde SQLite database uit je HitData map
    $db_path = '/var/www/html/HitData/hitjam.db';
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Zorg dat de game_status tabel de juiste kolommen heeft voor de democratische modus
    $db->exec("CREATE TABLE IF NOT EXISTS game_status (
        id INTEGER PRIMARY KEY,
        current_song_id INTEGER DEFAULT 0,
        round_active INTEGER DEFAULT 0,
        music_started INTEGER DEFAULT 0,
        start_time REAL DEFAULT 0,
        timestamp_einde REAL DEFAULT 0,
        gestart_door TEXT DEFAULT ''
    )");

    // Zorg dat de scoretabel de gekozen antwoorden live kan bijhouden [INDEX]
    try {
        $db->exec("ALTER TABLE scores ADD COLUMN gekozen_jaar INTEGER DEFAULT 0");
    } catch(Exception $e) { /* Bestaat al */ }

    // Zorg voor de initiële statusrij
    $checkStatus = $db->query("SELECT COUNT(*) FROM game_status")->fetchColumn();
    if ($checkStatus == 0) {
        $db->exec("INSERT INTO game_status (id, current_song_id, round_active) VALUES (1, 0, 0)");
    }

} catch (Exception $e) {
    die("Database verbindingsfout in HJ2: " . $e->getMessage());
}
?>
