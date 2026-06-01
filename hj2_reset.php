<?php
require_once('hj2_db.php');
// Zet de spelstatus terug naar de rustige lobby-stand
$db->exec("UPDATE game_status SET round_active = 0, music_started = 0, current_song_id = 0, gestart_door = '' WHERE id = 1");
// Wis ook direct de tijdelijke antwoorden van de spelers
$db->exec("UPDATE scores SET gekozen_jaar = 0");

echo "🔄 De HitJam2 status is succesvol gereset naar de Lobby! Je kunt dit venster sluiten.";
?>
