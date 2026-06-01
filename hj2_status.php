<?php
session_start();
if (!isset($_SESSION['loggedin'])) { 
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Niet ingelogd']); 
    exit; 
}

require_once('hj2_db.php');

// 1. Haal de algemene spelstatus op
$status = $db->query("SELECT * FROM game_status WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$options = [];
$preview_url = "";
$song_info = null;
$resterende_tijd = 0;

// Als er een actieve battle is gestart door een speler
if ($status && $status['round_active'] == 1) {
    $song_id = $status['current_song_id'];
    $start_time = (float)$status['start_time'];
    $huidige_tijd = microtime(true);
    
    // Bereken hoeveel seconden de ronde al loopt (maximaal 30 seconden)
    $verstreken_tijd = $huidige_tijd - $start_time;
    $resterende_tijd = max(0, 30 - $verstreken_tijd);
    
    // 🔥 AUTOMATISCHE TIMEOUT: Als de 30 seconden voorbij zijn, sluit de ronde automatisch!
    if ($resterende_tijd <= 0) {
        $db->exec("UPDATE game_status SET round_active = 0, music_started = 0 WHERE id = 1");
        $status['round_active'] = 0;
    } else {
        // Haal de gegevens van het actieve liedje op
        $stmt = $db->prepare("SELECT artist, title, year FROM game_songs WHERE id = ?");
        $stmt->execute([$song_id]);
        $song_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($song_info) {
            $real_year = (int)$song_info['year'];
            
            // Genereer de 4 unieke jaarknoppen (eenmalig, gesorteerd)
            $years_pool = [$real_year];
            while (count($years_pool) query("SELECT username, points, gekozen_jaar FROM scores ORDER BY points DESC")->fetchAll(PDO::FETCH_ASSOC);

// 3. Stuur het complete overzicht als JSON terug naar de spelers
header('Content-Type: application/json');
echo json_encode([
    'round_active' => (int)$status['round_active'],
    'gestart_door' => $status['gestart_door'],
    'current_song_id' => (int)$status['current_song_id'],
    'resterende_tijd' => round($resterende_tijd, 1),
    'preview_url' => $preview_url,
    'options' => $options,
    'scorebord' => $scorebord,
    // We sturen de liedjesnaam en artiest ALTIJD mee, maar JavaScript verbergt dit 
    // totdat de speler heeft geklikt of de ronde voorbij is!
    'song_details' => $song_info ? [
        'title' => $song_info['title'],
        'artist' => $song_info['artist'],
        'year' => $song_info['year']
    ] : null
]);
?>
