<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit; }
require_once('hj2_db.php');

header('Content-Type: application/json');

// Kies een willekeurige hit uit de 70 nummers
$song = $db->query("SELECT id FROM game_songs ORDER BY RANDOM() LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if ($song) {
    $song_id = $song['id'];
    $huidige_tijd = microtime(true);
    $username = $_SESSION['user'];

    // Zet de ronde centraal actief voor IEDEREEN
    $stmt = $db->prepare("UPDATE game_status SET current_song_id = ?, round_active = 1, start_time = ?, gestart_door = ? WHERE id = 1");
    $stmt->execute([$song_id, $huidige_tijd, $username]);

    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
exit;
?>
