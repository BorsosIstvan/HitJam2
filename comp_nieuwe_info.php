<!-- Bouwsteen: Interactieve Infokaart & Spelknop in één -->
<div class="song-info-card" id="infoCard" onclick="verwerkKaartKlik()" style="background: rgba(255, 255, 255, 0.04); padding: 25px 20px; border-radius: 24px; border: 2px solid #ff2d55; margin: 25px 0; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 8px 25px rgba(255, 45, 85, 0.1);">
    
    <!-- STANDAARD TEXT (Als de gegevens nog geheim zijn) -->
    <div id="infoGeheimTxt" style="font-size: 18px; font-weight: bold; color: #ff2d55; text-transform: uppercase; letter-spacing: 1px; padding: 20px 0;">
        👁️ Klik om te onthullen
    </div>

    <!-- DE GEGEVENS (Standaard onzichtbaar via opacity: 0) -->
    <div id="infoDataSectie" style="display: none; animation: fadeInHJ2 0.4s ease;">
        <div class="info-year" style="font-size: 56px; font-weight: 900; color: #ff9500; margin-bottom: 5px;"><?= $song['year'] ?></div>
        <div class="info-title" style="font-size: 22px; font-weight: 800; margin-bottom: 5px;"><?= htmlspecialchars($song['title']) ?></div>
        <div class="info-artist" style="color: #b3b3b3; font-size: 16px; margin-bottom: 15px;"><?= htmlspecialchars($song['artist']) ?></div>
        
        <div style="font-size: 11px; color: #00ffcc; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; background: rgba(0,255,204,0.05); padding: 6px; border-radius: 8px; display: inline-block;">
            🔄 Klik nogmaals voor volgende hit
        </div>
    </div>
</div>

<style>
@keyframes fadeInHJ2 { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
// We houden bij of de kaart open (onthuld) staat
let kaartStaatOpen = false;

function verwerkKaartKlik() {
    const audio = document.getElementById('soloAudio');
    const playBtn = document.getElementById('playBtn');
    const kaart = document.getElementById('infoCard');

    if (!kaartStaatOpen) {
        // --- 1. ACTIE: GEGEVENS ONTHULLEN ---
        kaartStaatOpen = true;

        // Stop de muziek direct
        if (audio) audio.pause();
        if (playBtn) { playBtn.innerHTML = "▶️"; playBtn.classList.remove('playing'); }

        // Verberg de geheime tekst en toon de echte liedjesgegevens
        document.getElementById('infoGeheimTxt').style.display = 'none';
        document.getElementById('infoDataSectie').style.display = 'block';

        // Verander het uiterlijk van de kaart naar de "Volgende" stijl (Blauw/Groene gloed)
        kaart.style.borderColor = "#00ffcc";
        kaart.style.boxShadow = "0 8px 25px rgba(0, 255, 204, 0.2)";

    } else {
        // --- 2. ACTIE: VOLGENDE LIEDJE LADEN ---
        // Controleer of we in de multiplayer modus zitten
        if (typeof mpRondeId !== "undefined" && window.location.search.includes('multiplayer=1')) {
            window.location.href = 'speel.php';
        } else {
            window.location.href = 'speel.php';
        }
    }
}

// 🔥 NAADLOZE KOPPELING MET DE QUIZ-BOUWSTEEN
// Als een speler op een jaarknop klikt in comp_quiz.php, moet de kaart OOK direct openklappen!
if (typeof controleerJaar === "function") {
    const origineleControleerJaar = controleerJaar;
    controleerJaar = function(knopElement, gekozenJaar, correctJaar) {
        // Voer eerst de normale goed/fout logica uit
        origineleControleerJaar(knopElement, gekozenJaar, correctJaar);
        
        // Laat de kaart direct transformeren zonder dat je erop hoeft te klikken!
        kaartStaatOpen = true;
        document.getElementById('infoGeheimTxt').style.display = 'none';
        document.getElementById('infoDataSectie').style.display = 'block';
        document.getElementById('infoCard').style.borderColor = "#00ffcc";
        document.getElementById('infoCard').style.boxShadow = "0 8px 25px rgba(0, 255, 204, 0.2)";
    };
}
</script>
