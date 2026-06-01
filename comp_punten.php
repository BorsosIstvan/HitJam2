<?php
// Zorg dat de score-variabelen bestaan in de sessie van deze speler
if (!isset($_SESSION['score_totaal'])) { $_SESSION['score_totaal'] = 0; }
if (!isset($_SESSION['score_streak'])) { $_SESSION['score_streak'] = 0; }

// Haal de actuele stand op uit de sessie
$huidige_score = $_SESSION['score_totaal'];
$huidige_streak = $_SESSION['score_streak'];
?>

<!-- HTML Structuur voor de Puntenbalk bovenin -->
<div class="score-display-box" style="display: flex; justify-content: space-between; background: rgba(255, 255, 255, 0.05); padding: 12px 18px; border-radius: 14px; margin: 10px 0 20px 0; border: 1px solid rgba(255, 255, 255, 0.08); font-size: 14px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase;">
    <div style="color: #00ffcc;">
        🏆 Score: <span id="localScore" style="color: #ffffff; font-size: 16px; font-weight: 900;"><?= $huidige_score ?></span>
    </div>
    <div style="color: #ff9500;" id="streakWrapper">
        🔥 Streak: <span id="localStreak" style="color: #ffffff; font-size: 16px; font-weight: 900;"><?= $huidige_streak ?></span>
    </div>
</div>

<!-- AJAX Helper: Onzichtbaar formulier om scores op de achtergrond naar de database te sturen [INDEX] -->
<script>
function updateDatabaseScore(puntenErbij, nieuweStreak) {
    let formData = new FormData();
    formData.append('punten_erbij', puntenErbij);
    formData.append('nieuwe_streak', nieuweStreak);

    // Stuur de score-update direct op de achtergrond naar de Pi
    fetch('hj2_update_score.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            console.log("Database score bijgewerkt:", data);
        })
        .catch(e => console.error("Database score synchronisatie mislukt:", e));
}
</script>
