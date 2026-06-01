<?php
// 🔥 Schakel alle foutmeldingen live in op je scherm
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // 1. Koppel met de database
    require_once('hj2_db.php');

    // 2. Probeer de status te resetten naar de lobby-stand
    $db->exec("UPDATE game_status SET round_active = 0, music_started = 0, current_song_id = 0, gestart_door = '' WHERE id = 1");
    
    // 3. Wis de tijdelijke antwoorden van spelers voor de monitor
    $db->exec("UPDATE scores SET gekozen_jaar = 0");

    echo "<div style='font-family:sans-serif; padding:20px; background:#28a745; color:white; border-radius:10px; max-width:500px; margin:40px auto; text-align:center;'>";
    echo "<h3>🎉 Succesvol Gereset!</h3>";
    echo "<p>De database staat weer in de lobby-stand. Je kunt nu een nieuwe battle starten!</p>";
    echo "</div>";

} catch (Exception $e) {
    // Als er een typefout in de SQL zit of een kolom mist, zie je dat hier direct rood op wit!
    echo "<div style='font-family:sans-serif; padding:20px; background:#dc3545; color:white; border-radius:10px; max-width:500px; margin:40px auto;'>";
    echo "<h3>❌ SQLite Fout gevonden:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
