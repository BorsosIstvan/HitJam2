<?php
session_start();
if (!isset($_SESSION['loggedin'])) { 
    header("Location: login.php"); 
    exit; 
}
$huidige_speler = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>HitJam 2 - Battle Arena</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #0b0c10; color: #ffffff; display: flex; justify-content: center; min-height: 100vh; }
        .app-container { width: 100%; max-width: 450px; background: linear-gradient(180deg, #160c1b 0%, #0b0c10 100%); padding: 25px 20px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 0 30px rgba(0,0,0,0.6); text-align: center; }
        .logo { font-size: 32px; font-weight: 900; background: linear-gradient(45deg, #ff2d55, #ff9500); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; margin: 0; }
        .status-bar { background: rgba(255,255,255,0.05); padding: 8px; border-radius: 10px; font-size: 12px; color: #aaa; margin: 10px 0; }
        .card-box { background: rgba(255, 255, 255, 0.04); padding: 20px; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.08); margin: 15px 0; }
        .score-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 15px; }
        .score-row:last-child { border: none; }
        .badge-choice { background: #007bff; padding: 2px 6px; border-radius: 8px; font-size: 12px; font-weight: bold; }
        .btn { width: 100%; padding: 16px; border-radius: 16px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; transition: all 0.2s; text-transform: uppercase; }
        .btn-action { background: linear-gradient(90deg, #ff2d55, #ff9500); color: white; box-shadow: 0 4px 15px rgba(255, 45, 85, 0.3); }
        .btn-unmute { background: #00ffcc; color: #0b0c10; font-size: 18px; margin: 20px 0; animation: bounce 1s infinite alternate; }
        .btn-choice { padding: 20px 10px; border-radius: 16px; font-size: 22px; font-weight: 900; border: 2px solid #33343f; background: #1f2026; color: white; }
        .btn-back { background: #1f2026; color: #fff; border: 1px solid #33343f; text-decoration: none; display: block; text-align: center; box-sizing: border-box; }
        .btn:active { transform: scale(0.96); }
        @keyframes bounce { from { transform: scale(0.96); } to { transform: scale(1.02); } }
        .choices-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px; }
        .timer-txt { font-size: 28px; font-weight: 900; color: #ff2d55; margin: 10px 0; }
    </style>
</head>
<body>

    <div class="app-container">
        <div>
            <h1 class="logo">HitJam 2</h1>
            <div class="status-bar">Speler: <span style="color:#ff9500; font-weight:bold;"><?= htmlspecialchars($huidige_speler) ?></span></div>

            <!-- HTML5 AUDIO TAG -->
            <audio id="hj2Audio"></audio>

            <!-- LOBBY INTERFACE (Status A) -->
            <div id="lobbyView">
                <button class="btn btn-action" onclick="startNieuweBattle()">🚀 Start Nieuwe Battle</button>
                <button class="btn" style="background:#222; color:#ff2d55; margin-top:10px; font-size:12px; padding:8px;" onclick="noodReset()">🔄 Reset Vastgelopen Ronde</button>
                
                <div class="card-box">
                    <h3 style="margin-top:0; font-size:14px; text-transform:uppercase; color:#aaa; letter-spacing:1px;">🏆 Ranglijst & Spelers</h3>
                    <div id="lobbyScores">Lobby laden...</div>
                </div>
            </div>

            <!-- UNMUTE PANEEEL -->
            <div id="unmuteView" style="display:none;">
                <p style="color:#00ffcc; font-weight:bold;">🔊 De battle is gestart!</p>
                <button class="btn btn-unmute" onclick="activeerGeluidEnQuiz()">🎵 Klik voor Geluid & Quiz</button>
            </div>

            <!-- QUIZ SPEELVELD (Status B) -->
            <div id="quizView" style="display:none;">
                <div class="timer-txt">⏱️ <span id="timerCountdown">30</span>s</div>
                <p style="color:#aaa; font-size:14px; margin:0;">Kies bliksemsnel het juiste jaartal!</p>
                <div class="choices-grid" id="choicesGrid"></div>
            </div>

            <!-- RESULTAAT INTERFACE (Status C & D) -->
            <div id="resultView" style="display:none;">
                <div id="resultBadge" style="font-size:24px; font-weight:900; margin-bottom:15px;"></div>
                <div class="card-box" style="border-color:#ff9500;">
                    <div style="font-size:42px; font-weight:900; color:#ff9500;" id="resYear">----</div>
                    <div style="font-size:18px; font-weight:800; margin:5px 0;" id="resTitle">Titel</div>
                    <div style="color:#aaa; font-size:14px;" id="resArtist">Artiest</div>
                </div>
                
                <div class="card-box">
                    <h4 style="margin-top:0; font-size:12px; text-transform:uppercase; color:#aaa;">⚡ Live Antwoorden deze ronde</h4>
                    <div id="liveRoundAnswers"></div>
                </div>
            </div>
        </div>

        <a href="index.php" class="btn btn-back">⬅ nighttime; Terug naar Hoofdscherm</a>
    </div>

    <!-- HIER KOMT HET JS COMPONENT UIT DE VOLGENDE STAP -->
	    <script>
        const huidigeSpeler = "<?= $huidige_speler ?>";
        let momenteelRondeId = 0;
        let alGedruktDezeRonde = false;
        let geluidGeactiveerd = false;

        // De live-stream ticker die elke seconde de Pi polst
        // De live-stream ticker met actieve foutopsporing
		setInterval(function() {
			fetch('hj2_status.php')
				.then(response => {
					if (!response.ok) {
						throw new Error("HTTP Foutcode: " + response.status);
					}
					return response.text(); // Pak eerst tekst om PHP-fouten te kunnen vangen
				})
				.then(text => {
					try {
						const data = JSON.parse(text);
						
						// Als de data goed aankomt, bouw het scorebord
						if (data.scorebord) {
							bouwScorebord(data.scorebord, data.round_active);
						}

						if (data.round_active === 1) {
							const audio = document.getElementById('hj2Audio');
							if (audio && audio.src !== data.preview_url) {
								audio.src = data.preview_url;
							}

							if (data.current_song_id !== momenteelRondeId) {
								momenteelRondeId = data.current_song_id;
								alGedruktDezeRonde = false;
								geluidGeactiveerd = false;
								
								document.getElementById('lobbyView').style.display = 'none';
								document.getElementById('resultView').style.display = 'none';
								document.getElementById('unmuteView').style.display = 'block';
							}

							if (document.getElementById('quizView').style.display === 'flex') {
								document.getElementById('timerCountdown').innerHTML = Math.ceil(data.resterende_tijd);
							}

							updateLiveAntwoorden(data.scorebord);
							
							if(data.song_details) {
								document.getElementById('resYear').innerHTML = data.song_details.year;
								document.getElementById('resTitle').innerHTML = data.song_details.title;
								document.getElementById('resArtist').innerHTML = data.song_details.artist;
							}
						} else {
							if (momenteelRondeId !== 0) {
								momenteelRondeId = 0;
								document.getElementById('quizView').style.display = 'none';
								document.getElementById('resultView').style.display = 'none';
								document.getElementById('unmuteView').style.display = 'none';
								document.getElementById('lobbyView').style.display = 'block';
								const audio = document.getElementById('hj2Audio');
								if (audio) audio.pause();
							}
						}
					} catch(e) {
						// 🔥 HIER VANGEN WE DE FOUT: Als hj2_status.php crasht, zie je hier de reden!
						alert("❌ Fout in hj2_status.php:\n" + text);
					}
				})
				.catch(err => {
					console.error("Netwerkfout:", err);
				});
		}, 1000);


        // Functie om de battle te starten
        function startNieuweBattle() {
            fetch('hj2_start_battle.php')
                .then(response => {
                    if (!response.ok) throw new Error("HTTP Foutcode: " + response.status);
                    return response.text();
                })
                .then(text => {
                    try {
                        const res = JSON.parse(text);
                        if (res.status === 'error') {
                            alert("🚨 Spelfout: " + res.message);
                        }
                    } catch(e) {
                        alert("❌ Server PHP Fout:\n" + text);
                    }
                })
                .catch(err => {
                    alert("🌐 Kan geen verbinding maken met de Pi: " + err.message);
                });
        }

        // Activeer het geluid na de browser-blokkade bypass
        function activeerGeluidEnQuiz() {
            geluidGeactiveerd = true;
            document.getElementById('unmuteView').style.display = 'none';
            document.getElementById('quizView').style.display = 'flex';
            
            let audio = document.getElementById('hj2Audio');
            audio.play().catch(e => console.log("Audio play geblokkeerd"));

            fetch('hj2_status.php')
                .then(r => r.json())
                .then(data => {
                    let html = '';
                    data.options.forEach(jaar => {
                        html += `<button class="btn btn-choice" onclick="stuurAntwoord(${jaar})">${jaar}</button>`;
                    });
                    document.getElementById('choicesGrid').innerHTML = html;
                });
        }

        // Verzend het gekozen jaartal
        function stuurAntwoord(gekozenJaar) {
            if (alGedruktDezeRonde) return;
            alGedruktDezeRonde = true;

            document.getElementById('quizView').style.display = 'none';
            document.getElementById('resultView').style.display = 'block';
            document.getElementById('resultBadge').innerHTML = "⏳ Controleren...";

            let formData = new FormData();
            formData.append('jaar', gekozenJaar);

            fetch('hj2_verwerk_antwoord.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(res => {
                    let badge = document.getElementById('resultBadge');
                    if (res.status === 'correct') {
                        badge.innerHTML = `<span style="color:#00ffcc; font-weight:900;">🎉 GOED!</span><br><span style="font-size:16px;">+${res.points} Punten</span>`;
                    } else {
                        badge.innerHTML = `<span style="color:#ff2d55; font-weight:900;">❌ FOUT!</span>`;
                    }
                })
                .catch(err => {
                    document.getElementById('resultBadge').innerHTML = "❌ Fout bij verwerken antwoord";
                });
        }

        // Nood-reset logica voor vastgelopen rondes
        function noodReset() {
            if (confirm("Weet je zeker dat je de huidige ronde wilt forceren te stoppen?")) {
                fetch('hj2_verwerk_antwoord.php?sluit_ronde=1')
                    .then(() => {
                        alert("🔄 Spel gereset naar de lobby!");
                        window.location.reload();
                    });
            }
        }

        // Bouw het scorebord in de lobby op
        function bouwScorebord_oud(lijst, rondeActief) {
            let html = '';
            lijst.forEach((speler, index) => {
                let statusIcoon = (rondeActief === 1 && speler.gekozen_jaar > 0) ? '✅' : '⏳';
                html += `<div class="score-row">
                            <span>${index+1}. <strong>${speler.username}</strong> ${statusIcoon}</span>
                            <span style="color:#00ffcc; font-weight:bold;">${speler.points} Pnt</span>
                         </div>`;
            });
            document.getElementById('lobbyScores').innerHTML = html;
        }
		
		// voor het test
		function bouwScorebord(lijst, rondeActief) {
			console.log("Live spelers ontvangen van server:", lijst); // Debug-lijn voor de console
			
			let html = '';
			
			// Als de lijst van de server leeg is
			if (!lijst || lijst.length === 0) {
				html = '<p style="color:#666; font-size:14px; margin:10px 0;">Geen actieve spelers gevonden...</p>';
				document.getElementById('lobbyScores').innerHTML = html;
				return;
			}

			// Loop door alle spelers die in de database staan
			lijst.forEach((speler, index) => {
				// Bepaal het status-icoontje (✅ als ze al gedrukt hebben, ⏳ als ze nog nadenken)
				let statusIcoon = (rondeActief === 1 && speler.gekozen_jaar > 0) ? '✅' : '⏳';
				
				html += `<div class="score-row">
							<span>${index + 1}. 👤 <strong style="color:#ff9500;">${speler.username}</strong> ${statusIcoon}</span>
							<span style="color:#00ffcc; font-weight:bold;">${speler.points} Pnt</span>
						 </div>`;
					});
					
			// Zet de HTML live in de lobby-box
			document.getElementById('lobbyScores').innerHTML = html;
		}


        // Toon live antwoorden
        function updateLiveAntwoorden(lijst) {
            let html = '';
            lijst.forEach(speler => {
                if (speler.gekozen_jaar > 0) {
                    html += `<div style="font-size:14px; margin:5px 0; text-align:left;">
                                👤 <strong>${speler.username}</strong> koos: <span class="badge-choice">${speler.gekozen_jaar}</span>
                             </div>`;
                }
            });
            document.getElementById('liveRoundAnswers').innerHTML = html || '<p style="color:#555;margin:0;">Wachten tot spelers klikken...</p>';
        }
    </script>


</body>
</html>
   