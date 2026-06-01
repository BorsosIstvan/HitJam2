<?php
// We halen het echte jaar uit de gekozen song
$echt_jaar = (int)$song['year'];

// Genereer 3 nep-jaartallen in de buurt van het echte jaar (maximaal 7 jaar verschil)
$jaren_lijst = [$echt_jaar];
while (count($jaren_lijst) < 4) {
    $nep_jaar = $echt_jaar + rand(-7, 7);
    if (!in_array($nep_jaar, $jaren_lijst) && $nep_jaar <= 2026) {
        $jaren_lijst[] = $nep_jaar;
    }
}
// Sorteer de jaartallen willekeurig
sort($jaren_lijst);
?>

<!-- HTML Structuur voor de Quiz Knoppen -->
<div id="quizSectie" style="margin: 20px 0;">
    <p id="quizInstructie" style="color:#aaa; font-size:14px; margin-bottom: 10px;">Kies het juiste uitgavejaar:</p>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <?php foreach ($jaren_lijst as $jaar): ?>
            <button class="btn btn-jaar-keuze" style="padding: 20px 10px; border-radius: 16px; font-size: 22px; font-weight: 900; border: 2px solid #33343f; background: #1f2026; color: white; cursor: pointer; transition: all 0.1s;" onclick="controleerJaar(this, <?= $jaar ?>, <?= $echt_jaar ?>)">
                <?= $jaar ?>
            </button>
        <?php endforeach; ?>
    </div>
    
    <!-- Vakje voor Directe Goed/Fout Feedback op het scherm -->
    <div id="quizFeedback" style="font-size: 24px; font-weight: 900; margin-top: 15px; display: none; text-transform: uppercase;"></div>
</div>

<script>
// 🔥 CRUCIALE FIX: Spatie wegehaald bij 'knopElement' zodat de functie correct start!
function controleerJaar(knopElement, gekozenJaar, correctJaar) {
    // 1. Schakel direct alle jaarknoppen uit
    const alleKnoppen = document.querySelectorAll('.btn-jaar-keuze');
    alleKnoppen.forEach(btn => btn.disabled = true);

    // 2. Stop de muziek onmiddellijk via de audio-bouwsteen
    const audio = document.getElementById('soloAudio');
    if (audio) audio.pause();
    const playBtn = document.getElementById('playBtn');
    if (playBtn) { playBtn.innerHTML = "▶️"; playBtn.classList.remove('playing'); }

    const feedbackDiv = document.getElementById('quizFeedback');
    feedbackDiv.style.display = 'block';

    // 3. Geef visuele feedback en bereken de score [INDEX]
    if (gekozenJaar === correctJaar) {
        knopElement.style.borderColor = '#00ffcc';
        knopElement.style.background = 'rgba(0, 255, 204, 0.1)';
        
        // Score berekening
        let huidigeStreak = parseInt(document.getElementById('localStreak').innerText) + 1;
        let puntenWinst = 50 + (huidigeStreak * 10);
        let huidigeScore = parseInt(document.getElementById('localScore').innerText) + puntenWinst;
        
        // Scherm direct updaten
        document.getElementById('localScore').innerText = huidigeScore;
        document.getElementById('localStreak').innerText = huidigeStreak;
        feedbackDiv.innerHTML = `<span style='color:#00ffcc;'>🎉 Goed! (+${puntenWinst} Pnt)</span>`;
        
        // Synchroniseer met SQLite database via de punten-bouwsteen [INDEX]
        if (typeof updateDatabaseScore === "function") {
            updateDatabaseScore(puntenWinst, huidigeStreak);
        }
    } else {
        knopElement.style.borderColor = '#ff2d55';
        knopElement.style.background = 'rgba(255, 45, 85, 0.1)';
        feedbackDiv.innerHTML = "<span style='color:#ff2d55;'>❌ Helaas Fout!</span>";
        
        // Reset streak op het scherm naar 0
        document.getElementById('localStreak').innerText = 0;
        
        // Synchroniseer de reset van de streak met de database [INDEX]
        if (typeof updateDatabaseScore === "function") {
            updateDatabaseScore(0, 0);
        }
        
        // Licht de juiste knop op in het groen zodat je het antwoord ziet
        alleKnoppen.forEach(btn => {
            if (parseInt(btn.innerText) === correctJaar) { 
                btn.style.borderColor = '#00ffcc'; 
                btn.style.background = 'rgba(0, 255, 204, 0.05)';
            }
        });
    }

    // 4. Activeer direct de onthullingskaart en wissel de menuknoppen om [INDEX]
    document.getElementById('infoCard').style.display = 'block';
    document.getElementById('revealBtn').style.display = 'none';
    document.getElementById('nextBtn').style.display = 'block';
}
</script>
