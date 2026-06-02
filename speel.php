<?php
session_start();
if (!isset($_SESSION['loggedin'])) { 
    header("Location: login.php"); 
    exit; 
}

require_once('hj2_db.php');

/*
try {
    $stmt = $db->query("SELECT id, artist, title, year FROM game_songs ORDER BY RANDOM() LIMIT 1");
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$song) {
        die("<p style='color:red; text-align:center; margin-top:50px;'>Fout: De database is leeg.</p>");
    }
} catch (Exception $e) {
    die("Database fout: " . $e->getMessage());
}
*/
try {
    // 🔥 MULTIPLAYER UPDATE: Als er een ID is meegegeven door de groepsbattle, laad die! [INDEX]
    if (isset($_GET['id'])) {
        $stmt = $db->prepare("SELECT id, artist, title, year FROM game_songs WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $song = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Anders laden we gewoon een random nummer (solo-modus)
        $stmt = $db->query("SELECT id, artist, title, year FROM game_songs ORDER BY RANDOM() LIMIT 1");
        $song = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if (!$song) { die("Liedje niet gevonden."); }
} catch (Exception $e) { die("Database fout: " . $e->getMessage()); }


$schone_artiest = str_replace('&', ' ', $song['artist']);
$zoekterm = urlencode($schone_artiest . " " . $song['title']);
$api_url = "https://itunes.apple.com/search?term=" . $zoekterm . "&limit=1&entity=song";

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
    die("<p style='color:red; text-align:center; margin-top:50px;'>Fout: Geen audio gevonden.</p>");
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>HitJam 2 - Track Info</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #0b0c10; color: #ffffff; display: flex; justify-content: center; min-height: 100vh; }
        .app-container { width: 100%; max-width: 450px; background: linear-gradient(180deg, #160c1b 0%, #0b0c10 100%); padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 0 30px rgba(0,0,0,0.6); text-align: center; }
        .logo { font-size: 32px; font-weight: 900; background: linear-gradient(45deg, #ff2d55, #ff9500); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; }
        
        .play-box { margin: 20px 0; }
        .btn-audio { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #ff2d55, #e01b43); border: none; color: white; font-size: 40px; cursor: pointer; box-shadow: 0 8px 25px rgba(255, 45, 85, 0.4); transition: all 0.2s; }
        .btn-audio.playing { background: #121212; border: 3px solid #ff2d55; color: #ff2d55; box-shadow: none; }
        
        /* De stijlvolle infokaart (standaard verborgen via display: none) */
        .song-info-card { display: none; background: rgba(255, 255, 255, 0.04); padding: 20px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.1); margin: 20px 0; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .info-year { font-size: 54px; font-weight: 900; color: #ff9500; margin-bottom: 5px; }
        .info-title { font-size: 22px; font-weight: 800; margin-bottom: 5px; }
        .info-artist { color: #b3b3b3; font-size: 16px; }

        .btn { width: 100%; padding: 16px; border-radius: 14px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; transition: all 0.2s; text-transform: uppercase; }
        .btn-reveal { background: #ffffff; color: #0b0c10; box-shadow: 0 4px 15px rgba(255,255,255,0.1); margin-bottom: 15px; }
        .btn-next { background: linear-gradient(90deg, #007bff, #00ffcc); color: white; display: none; margin-bottom: 15px; text-decoration: none; text-align: center; box-sizing: border-box; }
        .btn-back { background: #1f2026; color: white; border: 1px solid #33343f; text-decoration: none; display: block; text-align: center; box-sizing: border-box; }
        .btn:active { transform: scale(0.96); }
		
		        /* Footer */
        .footer {
            font-size: 11px;
            color: #4f4f4f;
            text-align: center;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <div class="app-container">
        <div>
            <h1 class="logo">HitJam 2</h1>
            <p style="color:#aaa; font-size:14px;">Gebouwd met onafhankelijke bouwstenen!</p>
        </div>
				
		<!-- 🧱 BOUWSTEEN 5: NIEUW! LIVE SCORE & STREAK DISLPAY -->
		<?php require_once('comp_punten.php'); ?>

        <!-- 🧱 BOUWSTEEN 1: AUDIO CONTROLLER -->
        <?php require_once('comp_audio.php'); ?>
		
		<!-- 🧱 BOUWSTEEN 4: NIEUW! MEERKEUZE QUIZ KNOPPEN -->
        <?php require_once('comp_quiz.php'); ?>
		
        <!-- 🧱 BOUWSTEEN 2: GEHEIME INFOKAART -->
        <?php require_once('comp_nieuwe_info.php'); ?>
		
		<!-- 🧱 BOUWSTEEN 3: ACTIEKNOPPEN -->
        <?php //require_once('comp_knoppen.php'); ?>

		<!-- 🧱 BOUWSTEEN 6: NIEUW! LIVE MULTIPLAYER RANGLIJST -->
        <?php //require_once('comp_ranglijst.php'); ?>
		
		<!-- 🧱 BOUWSTEEN 7: NIEUW! MULTIPLAYER SYNCHRONISATIE -->
		<?php //require_once('comp_multiplayer_sync.php'); ?>
		
		<!-- Footer -->
        <div class="footer">
            POWERED BY JBL & APPLE MUSIC
        </div>

    </div>

    <!-- HIER KOMT DE JAVASCRIPT UIT STAP 3B -->
	<script>
    // 🔥 FIX: Zorg dat de ranglijst-bouwsteen weet wie de huidige speler is!
    const huidigeSpeler = "<?= $_SESSION['user'] ?>";
	</script>

</body>
</html>
