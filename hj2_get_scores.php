<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit; }
require_once('hj2_db.php');

header('Content-Type: application/json');

try {
    // Haal alle spelers op gesorteerd op de hoogste score [INDEX]
    $spelers = $db->query("SELECT username, points FROM scores ORDER BY points DESC")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($spelers);
} catch (Exception $e) {
    echo json_encode([]);
}
exit;
?>
