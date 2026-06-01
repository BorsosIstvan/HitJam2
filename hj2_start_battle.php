<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit; }
require_once('hj2_db.php');

header('Content-Type: application/json');

try {
    // Controleer of er al een ronde actief is
    $status = $db->query("SELECT round_active FROM game_status WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

    if ($status && (int)$status['round_active'] === 1) {
        echo json_encode(['status' => 'error', 'message' => 'Er loopt al een battle! Wacht tot deze voorbij is.']);
        exit;
    }

    // Pak een willekeurig nummer uit de nieuwe database
    $song = $db->query("SELECT id FROM game_songs ORDER BY RANDOM() LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    if ($song) {
        $song_id = $song['id'];
        $huidige_tijd = microtime(true);
        $username = $_SESSION['user'];

        // Activeer de ronde live in SQLite
        $stmt = $db->prepare("UPDATE game_status SET current_song_id = ?, round_active = 1, music_started = 1, start_time = ?, gestart_door = ? WHERE id = 1");
        $stmt->execute([$song_id, $huidige_tijd, $username]);

        // Wis direct de gekozen antwoorden van de vorige ronde [INDEX]
        $db->exec("UPDATE scores SET gekozen_jaar = 0");

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Fout: Geen liedjes gevonden. Open eerst hj2_import.php!']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'SQLite Fout: ' . $e->getMessage()]);
}
exit;
?>
