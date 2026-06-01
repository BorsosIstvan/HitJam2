<!-- Bouwsteen: Multiplayer Sync Controller -->
<div class="card-box" style="background: rgba(255, 255, 255, 0.03); padding: 15px 20px; border-radius: 20px; border: 1px solid rgba(0, 123, 255, 0.2); margin: 15px 0;">
    <button class="btn" id="mpStartBtn" style="background: linear-gradient(90deg, #007bff, #00ffcc); color: white; display: block; width: 100%;" onclick="activeerGroepsBattle()">🚀 Start Groeps Battle</button>
    <div id="mpStatusTxt" style="font-size: 13px; color: #aaa; margin-top: 10px; display: none;"></div>
</div>

<script>
// 🔥 CRUCIALE FIX: Lees direct het actieve ID uit de URL van de browser
const urlParams = new URLSearchParams(window.location.search);
let mpRondeId = parseInt(urlParams.get('id')) || 0;

function activeerGroepsBattle() {
    fetch('hj2_trigger_battle.php')
        .then(r => r.json())
        .then(data => {
            if(data.status === 'success') {
                console.log("Groeps battle succesvol geactiveerd!");
            }
        });
}

// Deze check-loop luistert of er centraal een nieuwe battle start
setInterval(function() {
    fetch('hj2_sync_loop.php')
        .then(r => r.json())
        .then(data => {
            const statusTxt = document.getElementById('mpStatusTxt');
            const startBtn = document.getElementById('mpStartBtn');

            if (data.round_active === 1) {
                startBtn.style.display = 'none';
                statusTxt.style.display = 'block';
                statusTxt.innerHTML = `🔥 Groeps Battle actief! Gestart door: <strong style='color:#ff9500;'>${data.gestart_door}</strong><br>⏱️ Tijd over: <strong style='color:#ff2d55;'>${Math.ceil(data.resterende_tijd)}s</strong>`;
                
                // 🔥 ENORME UPGRADE: Alleen herladen als het centrale ID ECHT verschilt van de URL!
                if (parseInt(data.current_song_id) !== mpRondeId) {
                    mpRondeId = parseInt(data.current_song_id);
                    // Stuur de browser synchroon naar de nieuwe quiz-ronde
                    window.location.href = 'speel.php?id=' + data.current_song_id + '&multiplayer=1';
                }
            } else {
                startBtn.style.display = 'block';
                statusTxt.style.display = 'none';
                // Als de ronde op de server is afgelopen, maar we staan nog in de multiplayer-link:
                if (window.location.search.includes('multiplayer=1')) {
                    // Stuur iedereen netjes terug naar de lege solo-modus/lobby
                    window.location.href = 'speel.php';
                }
            }
        })
        .catch(e => console.error("Sync loop weigert:", e));
}, 1000);
</script>

