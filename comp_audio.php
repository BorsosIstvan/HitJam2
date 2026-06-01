<!-- Bouwsteen: Audio Controller -->
<audio id="soloAudio" src="<?= $preview_url ?>"></audio>

<div class="play-box" style="margin: 20px 0;">
    <button class="btn-audio" id="playBtn" onclick="toggleMuziek()">▶️</button>
</div>

<script>
function toggleMuziek() {
    const audio = document.getElementById('soloAudio');
    const playBtn = document.getElementById('playBtn');
    if (audio.paused) {
        audio.play()
            .then(() => {
                playBtn.innerHTML = "⏸️";
                playBtn.classList.add('playing');
            })
            .catch(err => alert("Klik nogmaals voor geluid!"));
    } else {
        audio.pause();
        playBtn.innerHTML = "▶️";
        playBtn.classList.remove('playing');
    }
}

document.getElementById('soloAudio').onended = function() {
    document.getElementById('playBtn').innerHTML = "▶️";
    document.getElementById('playBtn').classList.remove('playing');
};
</script>
