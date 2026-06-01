<?php
session_start();
// Veiligheid: Alleen ingelogde gebruikers mogen spelen
if (!isset($_SESSION['loggedin'])) { 
    header("Location: login.php"); 
    exit; 
}

require_once('hj2_db.php');

try {
    // Pak 1 willekeurig liedje uit de nieuwe database
    $stmt = $db->query("SELECT id, artist, title, year FROM game_songs ORDER BY RANDOM() LIMIT 1");
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$song) {
        die("<p style='color:red; text-align:center; margin-top:50px;'>Fout: De database is leeg. Voer eerst hj2_import.php uit!</p>");
    }
} catch (Exception $e) {
    die("Database fout: " . $e->getMessage());
}

// Haal de 30-seconden preview op via Apple Music [INDEX]
$schone_artiest = str_replace('&', ' ', $song['artist']);
$zoekterm = urlencode($schone_artiest . " " . $song['title']);
$api_url = "https://apple.com" . $zoekterm . "&limit=1&entity=song";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
curl_close($ch);

$preview_url = "";
if ($response) {
    $json = json_decode($response, true);
    if (isset($json['results'][0]['previewUrl'])) {
        $preview_url = $json['results'][0]['previewUrl'];
    }
}

if (empty($preview_url)) {
    die("<p style='color:red; text-align:center; margin-top:50px;'>Fout: Apple Music heeft geen audio gevonden voor " . htmlspecialchars($song['artist'] . " - " . $song['title']) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>HitJam 2 - Solo Player</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #0b0c10; color: #ffffff; display: flex; justify-content: center; min-height: 100vh; }
        .app-container { width: 100%; max-width: 450px; background: linear-gradient(180deg, #160c1b 0%, #0b0c10 100%); padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 0 30px rgba(0,0,0,0.6); text-align: center; }
        .logo { font-size: 32px; font-weight: 900; background: linear-gradient(45deg, #ff2d55, #ff9500); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; }
        
        .play-box { margin: auto; }
        .btn-audio { width: 140px; height: 140px; border-radius: 50%; background: linear-gradient(135deg, #ff2d55, #e01b43); border: none; color: white; font-size: 45px; cursor: pointer; box-shadow: 0 8px 25px rgba(255, 45, 85, 0.4); transition: all 0.2s; }
        .btn-audio.playing { background: #121212; border: 3px solid #ff2d55; color: #ff2d55; box-shadow: none; }
        
        .btn-back { width: 100%; padding: 16px; border-radius: 16px; font-size: 16px; font-weight: bold; border: 1px solid #33343f; background: #1f2026; color: white; text-decoration: none; display: block; text-align: center; box-sizing: border-box; }
    </style>
</head>
<body>

    <div class="app-container">
        <div>
            <h1 class="logo">HitJam 2</h1>
            <p style="color:#aaa; font-size:14px;">Klik op de knop om te testen of het geluid werkt!</p>
        </div>

        <!-- HTML5 AUDIO COMPONENT -->
        <audio id="soloAudio" src="<?= $preview_url ?>"></audio>

        <div class="play-box">
            <!-- Grote Play / Stop knop -->
            <button class="btn-audio" id="playBtn" onclick="toggleMuziek()">▶️</button>
        </div>

        <a href="index.php" class="btn-back">⬅️ Terug naar Menu</a>
    </div>

    <!-- HIER KOMT DE JAVASCRIPT UIT STAP 2 -->
	    <script>
        const audio = document.getElementById('soloAudio');
        const playBtn = document.getElementById('playBtn');

        function toggleMuziek() {
            if (audio.paused) {
                audio.play()
                    .then(() => {
                        playBtn.innerHTML = "⏸️";
                        playBtn.classList.add('playing');
                    })
                    .catch(err => {
                        alert("Browser blokkeert audio. Klik nogmaals!");
                    });
            } else {
                audio.pause();
                playBtn.innerHTML = "▶️";
                playBtn.classList.remove('playing');
            }
        }

        // Als de 30 seconden preview om is, zet de knop automatisch terug
        audio.onended = function() {
            playBtn.innerHTML = "▶️";
            playBtn.classList.remove('playing');
        };
    </script>


</body>
</html>
