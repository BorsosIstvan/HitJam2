<!-- Bouwsteen: Multiplayer Sync Controller -->
<div class="card-box" style="background: rgba(255, 255, 255, 0.03); padding: 15px 20px; border-radius: 20px; border: 1px solid rgba(0, 123, 255, 0.2); margin: 15px 0;">
    <button class="btn" id="mpStartBtn" style="background: linear-gradient(90deg, #007bff, #00ffcc); color: white; display: block;" onclick="activeerGroepsBattle()">🚀 Start Groeps Battle</button>
    <div id="mpStatusTxt" style="font-size: 13px; color: #aaa; margin-top: 10px; display: none;"></div>
</div>

<script>
let mpRondeId = 0;

function activeerGroepsBattle() {
    fetch('hj2_trigger_battle.php')
        .then(r => r.json())
        .then(data => {
            if(data.status === 'success') {
                console.log("Groeps battle succesvol geactiveerd!");
            }
        });
}

// Deze check-loop draait op de achtergrond en luistert of er centraal een battle start [INDEX]
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
                
                // Als er centraal een nieuw liedje is gekozen dat we nog niet afspelen [INDEX]
                if (data.current_song_id !== mpRondeId) {
                    mpRondeId = data.current_song_id;
                    
                    // Forceer het speelveld om dit centrale liedje te laden! [INDEX]
                    // We sturen de browser door naar speel.php met het specifieke ID van de groepsbattle
                    window.location.href = 'speel.php?id=' + data.current_song_id + '&multiplayer=1';
                }
            } else {
                startBtn.style.display = 'block';
                statusTxt.style.display = 'none';
                mpRondeId = 0;
            }
        });
}, 1000); // Check elke seconde [INDEX]
</script>
