<!-- Bouwsteen: Actieknoppen Control -->
<div>
    <button class="btn btn-reveal" id="revealBtn" onclick="onthulGegevens()">👁️ Onthul Gegevens</button>
    <a href="speel.php" class="btn btn-next" id="nextBtn" style="display: none; background: linear-gradient(90deg, #007bff, #00ffcc); color: white; margin-bottom: 15px; text-decoration: none; text-align: center; box-sizing: border-box;">🔄 Volgende Nummer</a>
    <a href="index.php" class="btn btn-back">⬅️ Hoofdmenu</a>
</div>

<script>
function onthulGegevens() {
    const audio = document.getElementById('soloAudio');
    if (audio) audio.pause();
    
    const playBtn = document.getElementById('playBtn');
    if (playBtn) { playBtn.innerHTML = "▶️"; playBtn.classList.remove('playing'); }

    document.getElementById('infoCard').style.display = 'block';
    document.getElementById('revealBtn').style.display = 'none';
    document.getElementById('nextBtn').style.display = 'block';
}
</script>
