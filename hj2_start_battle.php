<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit; }
require_once('hj2_db.php');

header('Content-Type: application/json');

// Controleer of er al een ronde actief is
$status = $db->query("SELECT round_active FROM game_status WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

if ($status && $status['round_active'] == 1) {
    echo json_encode(['status' => 'error', 'message' => 'Er loopt al een battle! Wacht tot deze voorbij is.']);
    exit;
}

// Kies een willekeurig nummer uit de database
$song = $db->query("SELECT id FROM game_songs ORDER BY RANDOM() LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if ($song) {
    $song_id = $song['id'];
    $huidige_tijd = microtime(true);
    $username = $_SESSION['user'];

    // Zet de nieuwe ronde live in de database
    $stmt = $db->prepare("UPDATE game_status SET current_song_id = ?, round_active = 1, music_started = 1, start_time = ?, gestart_door = ? WHERE id = 1");
    $stmt->execute([$song_id, $huidige_tijd, $username]);

    // Reset direct de gekozen antwoorden van de spelers voor deze nieuwe ronde [INDEX]
    $db->exec("UPDATE scores SET gekozen_jaar = 0");

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Geen liedjes gevonden in de database.']);
}
exit;
?>
