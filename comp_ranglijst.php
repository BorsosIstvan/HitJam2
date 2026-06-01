<!-- Bouwsteen: Live Ranglijst Multiplayers -->
<div class="card-box" style="background: rgba(255, 255, 255, 0.03); padding: 15px 20px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.08); margin-top: 25px; text-align: left;">
    <h3 style="margin-top:0; font-size:13px; text-transform:uppercase; color:#ff9500; letter-spacing:1px; margin-bottom: 12px; text-align: center;">🏆 Live Scorebord Multiplayers</h3>
    <div id="liveRanglijstContainer">
        <p style="color:#555; font-size:13px; margin:0; text-align:center;">Ranglijst laden...</p>
    </div>
</div>

<script>
function laadLiveRanglijst() {
    // We roepen een klein JSON-bestandje aan op de Pi
    fetch('hj2_get_scores.php')
        .then(response => response.json())
        .then(spelers => {
            if (!spelers || spelers.length === 0) return;
            
            let html = '';
            spelers.forEach((speler, index) => {
                // Highlight de huidige speler in het goud
                let isHuidige = (speler.username === huidigeSpeler) ? 'border-left: 3px solid #ff9500; padding-left: 5px;' : '';
                
                html += `<div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; ${isHuidige}">
                            <span>${index + 1}. 👤 <strong>${speler.username}</strong></span>
                            <span style="color:#00ffcc; font-weight:bold;">${speler.points} Pnt</span>
                         </div>`;
            });
            document.getElementById('liveRanglijstContainer').innerHTML = html;
        })
        .catch(e => console.error("Fout bij laden ranglijst:", e));
}

// Voer direct uit bij het laden van de pagina
laadLiveRanglijst();

// Ververs de ranglijst elke 3 seconden live op de achtergrond [INDEX]
setInterval(laadLiveRanglijst, 3000);
</script>
