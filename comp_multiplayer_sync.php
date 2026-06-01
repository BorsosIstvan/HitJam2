<!-- Bouwsteen: Multiplayer Sync Controller -->
<div class="card-box" style="background: rgba(255, 255, 255, 0.03); padding: 15px 20px; border-radius: 20px; border: 1px solid rgba(0, 123, 255, 0.2); margin: 15px 0;">
    <button class="btn" id="mpStartBtn" style="background: linear-gradient(90deg, #007bff, #00ffcc); color: white; display: block; width: 100%;" onclick="activeerGroepsBattle()">🚀 Start Groeps Battle</button>
    <div id="mpStatusTxt" style="font-size: 13px; color: #aaa; margin-top: 10px; display: none;"></div>
</div>

<script>
let mpRondeId = 0;

function activeerGroepsBattle() {
    fetch('hj2_trigger_battle.php')
        .then(r => {
            if(!r.ok) throw new Error("Netwerkfout " + r.status);
            return r.json();
        })
        .then(data => {
            if(data.status === 'success') {
                console.log("Groeps battle succesvol geactiveerd!");
            } else {
                alert("🚨 Fout bij starten: " + (data.message || "Onbekende fout"));
            }
        })
        .catch(err => alert("🌐 Verbindingsfout met Pi: " + err.message));
}

// Deze check-loop draait op de achtergrond en luistert of er een groepsbattle start [INDEX]
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
                
                // 🔥 CRUCIALE FIX: Alleen doorsturen en herladen als het ID ECHT verschilt van de huidige ronde! [INDEX]
                // Dit voorkomt dat de browser de muziek elke seconde reset of onderbreekt [INDEX].
                if (parseInt(data.current_song_id) !== parseInt(mpRondeId)) {
                    mpRondeId = data.current_song_id;
                    
                    // Schakel over naar het centrale liedje [INDEX]
                    window.location.href = 'speel.php?id=' + data.current_song_id + '&multiplayer=1';
                }
            } else {
                startBtn.style.display = 'block';
                statusTxt.style.display = 'none';
                mpRondeId = 0;
            }
        })
        .catch(e => console.error("Sync loop weigert:", e));
}, 1000); // Check elke seconde [INDEX]

</script>
