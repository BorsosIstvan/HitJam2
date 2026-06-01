<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit; }
require_once('hj2_db.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['user'];
    $punten_erbij = (int)$_POST['punten_erbij'];
    $nieuwe_streak = (int)$_POST['nieuwe_streak'];

    // Sla de score live op of update de bestaande score in SQLite [INDEX]
    $stmt = $db->prepare("INSERT INTO scores (username, points, gekozen_jaar) VALUES (?, ?, 0) 
                          ON CONFLICT(username) DO UPDATE SET points = points + ?");
    $stmt->execute([$username, $punten_erbij, $punten_erbij]);

    // Werk de lokale PHP sessie bij voor de veiligheid
    $_SESSION['score_totaal'] += $punten_erbij;
    $_SESSION['score_streak'] = $nieuwe_streak;

    echo json_encode(['status' => 'success', 'new_total' => $_SESSION['score_totaal']]);
    exit;
}
?>
