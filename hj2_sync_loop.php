<?php
session_start();
if (!isset($_SESSION['loggedin'])) { exit; }
require_once('hj2_db.php');

header('Content-Type: application/json');

// 1. Haal de centrale spelstatus op
$status = $db->query("SELECT * FROM game_status WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$preview_url = "";
$resterende_tijd = 0;

if ($status && (int)$status['round_active'] === 1) {
    $start_time = (float)$status['start_time'];
    $huidige_tijd = microtime(true);
    
    // Bereken de resterende tijd van de centrale 30-seconden timer
    $verstreken = $huidige_tijd - $start_time;
    $resterende_tijd = max(0, 30 - $verstreken);
    
    // Als de tijd om is, sluit de ronde automatisch voor iedereen
    if ($resterende_tijd <= 0) {
        $db->exec("UPDATE game_status SET round_active = 0 WHERE id = 1");
        $status['round_active'] = 0;
    } else {
        // Haal de artiest en titel op van het actieve multiplayer liedje
        $stmt = $db->prepare("SELECT artist, title FROM game_songs WHERE id = ?");
        $stmt->execute([$status['current_song_id']]);
        $song = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($song) {
            // Haal de preview op bij Apple Music [INDEX]
            $schone_artiest = str_replace('&', ' ', $song['artist']);
            $zoekterm = urlencode($schone_artiest . " " . $song['title']);
            //$api_url = "https://apple.com" . $zoekterm . "&limit=1&entity=song";
			$api_url = "https://itunes.apple.com/search?term=" . $zoekterm . "&limit=1&entity=song";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            curl_setopt($ch, CURLOPT_TIMEOUT, 4);
            $response = curl_exec($ch);
            curl_close($ch);
            
            if ($response) {
                $json = json_decode($response, true);
                if (isset($json['results'][0]['previewUrl'])) {
                    $preview_url = $json['results'][0]['previewUrl'];
                }
            }
        }
    }
}

// Stuur de multiplayer status terug als JSON
echo json_encode([
    'round_active' => (int)$status['round_active'],
    'current_song_id' => (int)$status['current_song_id'],
    'gestart_door' => $status['gestart_door'] ?? '',
    'resterende_tijd' => round($resterende_tijd, 1),
    'preview_url' => $preview_url
]);
exit;
?>
