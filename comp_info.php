<!-- Bouwsteen: Geheime Infokaart -->
<div class="song-info-card" id="infoCard" style="display: none; background: rgba(255, 255, 255, 0.04); padding: 20px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.1); margin: 20px 0; animation: fadeIn HJ2 0.4s ease;">
    <div class="info-year" style="font-size: 54px; font-weight: 900; color: #ff9500; margin-bottom: 5px;"><?= $song['year'] ?></div>
    <div class="info-title" style="font-size: 22px; font-weight: 800; margin-bottom: 5px;"><?= htmlspecialchars($song['title']) ?></div>
    <div class="info-artist" style="color: #b3b3b3; font-size: 16px;"><?= htmlspecialchars($song['artist']) ?></div>
</div>

<style>
@keyframes fadeInHJ2 { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
