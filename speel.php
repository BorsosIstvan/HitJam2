<?php
session_start();
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit; }

require_once('hj2_db.php');

try {
    $stmt = $db->query("SELECT id, artist, title, year FROM game_songs ORDER BY RANDOM() LIMIT 1");
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$song) { die("Database is leeg."); }
} catch (Exception $e) { die("Database fout: " . $e->getMessage()); }

$schone_artiest = str_replace('&', ' ', $song['artist']);
$zoekterm = urlencode($schone_artiest . " " . $song['title']);
//$api_url = "https://apple.com" . $zoekterm . "&limit=1&entity=song";
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
    if (isset($json['results']['previewUrl'])) { $preview_url = $json['results']['previewUrl']; }
}
if (empty($preview_url)) { die("Fout: Geen audio gevonden."); }
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>HitJam 2 - Modulair</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #0b0c10; color: #ffffff; display: flex; justify-content: center; min-height: 100vh; }
        .app-container { width: 100%; max-width: 450px; background: linear-gradient(180deg, #160c1b 0%, #0b0c10 100%); padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 0 30px rgba(0,0,0,0.6); text-align: center; }
        .logo { font-size: 32px; font-weight: 900; background: linear-gradient(45deg, #ff2d55, #ff9500); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; }
        .btn { width: 100%; padding: 16px; border-radius: 14px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; transition: all 0.2s; text-transform: uppercase; display: block; }
        .btn-reveal { background: #ffffff; color: #0b0c10; box-shadow: 0 4px 15px rgba(255,255,255,0.1); margin-bottom: 15px; }
        .btn-back { background: #1f2026; color: white; border: 1px solid #33343f; text-decoration: none; text-align: center; box-sizing: border-box; }
        .btn-audio { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #ff2d55, #e01b43); border: none; color: white; font-size: 40px; cursor: pointer; box-shadow: 0 8px 25px rgba(255, 45, 85, 0.4); }
        .btn-audio.playing { background: #121212; border: 3px solid #ff2d55; color: #ff2d55; box-shadow: none; }
    </style>
</head>
<body>

    <div class="app-container">
        <div>
            <h1 class="logo">HitJam 2</h1>
            <p style="color:#aaa; font-size:14px;">Gebouwd met onafhankelijke bouwstenen!</p>
        </div>

        <!-- 🧱 BOUWSTEEN 1: AUDIO CONTROLLER -->
        <?php require_once('comp_audio.php'); ?>

        <!-- 🧱 BOUWSTEEN 2: GEHEIME INFOKAART -->
        <?php require_once('comp_info.php'); ?>

        <!-- 🧱 BOUWSTEEN 3: ACTIEKNOPPEN -->
        <?php require_once('comp_knoppen.php'); ?>

    </div>

</body>
</html>
