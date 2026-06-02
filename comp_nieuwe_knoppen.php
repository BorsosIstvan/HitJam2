<!-- Bouwsteen: Slimme Dynamische Actieknop -->
<div style="margin-top: 20px;">
    <!-- De Centrale Actieknop -->
    <button class="btn btn-reveal" id="slimmeSpelKnop" onclick="verwerkKnopKlik()" style="margin-bottom: 15px;">
        👁️ Onthul Gegevens
    </button>
    
    <!-- De vaste knop om terug te keren naar het menu -->
    <a href="index.php" class="btn btn-back">⬅️ Hoofdmenu</a>
</div>

<script>
// We houden de status van de knop bij ('onthul' of 'volgende')
let huidigeKnopStatus = "onthul";

function verwerkKnopKlik() {
    const knop = document.getElementById('slimmeSpelKnop');

    if (huidigeKnopStatus === "onthul") {
        // --- ACTIE 1: ONTHUL DE GEGEVENS ---
        
        // Stop de audio direct via de audio-bouwsteen
        const audio = document.getElementById('soloAudio');
        if (audio) audio.pause();
        
        const playBtn = document.getElementById('playBtn');
        if (playBtn) { 
            playBtn.innerHTML = "▶️"; 
            playBtn.classList.remove('playing'); 
        }

        // Toon de verborgen infokaart (jaar, titel, artiest)
        document.getElementById('infoCard').style.display = 'block';

        // Verander de knop live naar de "Volgende" stand
        knop.innerHTML = "🔄 Volgende Nummer";
        knop.style.background = "linear-gradient(90deg, #007bff, #00ffcc)";
        knop.style.color = "white";
        knop.style.boxShadow = "0 4px 15px rgba(0, 123, 255, 0.3)";
        
        // Zet de status om zodat de volgende klik de pagina ververst
        huidigeKnopStatus = "volgende";

    } else if (huidigeKnopStatus === "volgende") {
        // --- ACTIE 2: VOLGENDE NUMMER STARTEN ---
        
        // Controleer of we in de multiplayer groepsbattle zitten
        if (typeof mpRondeId !== "undefined" && window.location.search.includes('multiplayer=1')) {
            // In multiplayer stuurt de knop je terug naar de schone speelpagina 
            // zodat de groeps-sync-loop een nieuwe battle kan opvangen
            window.location.href = 'speel.php';
        } else {
            // In solo-modus herlaadt de pagina direct voor een nieuw willekeurig nummer
            window.location.href = 'speel.php';
        }
    }
}

// 🔥 SLIMME KOPPELING MET DE QUIZ-BOUWSTEEN
// Als de speler op een jaarknop klikt in comp_quiz.php, moet de knop OOK direct transformeren!
// We overschrijven de oude onthul-functie zodat deze naadloos samenwerkt.
const origineleControleerJaar = typeof controleerJaar === "function" ? controleerJaar : null;

if (origineleControleerJaar) {
    controleerJaar = function(knopElement, gekozenJaar, correctJaar) {
        // Voer eerst de normale score- en kleurlogica uit van comp_quiz.php
        origineleControleerJaar(knopElement, gekozenJaar, correctJaar);
        
        // Transformeer de slimme knop direct naar de volgende stand
        const knop = document.getElementById('slimmeSpelKnop');
        knop.innerHTML = "🔄 Volgende Nummer";
        knop.style.background = "linear-gradient(90deg, #007bff, #00ffcc)";
        knop.style.color = "white";
        knop.style.boxShadow = "0 4px 15px rgba(0, 123, 255, 0.3)";
        huidigeKnopStatus = "volgende";
    };
}
</script>
