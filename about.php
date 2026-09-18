<?php
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Our Story & Philosophy';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero" style="background-image: linear-gradient(180deg, rgba(18, 36, 28, 0.4) 0%, rgba(18, 36, 28, 0.9) 100%), url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=2000&q=85');">
    <div>
        <span class="kicker">THE LIDO DE PARIS STORY</span>
        <h1>Hospitality, culture &amp; comfort.</h1>
        <p>A landmark hospitality destination situated in the heart of Manila's Chinatown along historic Ongpin Street.</p>
    </div>
</section>

<section class="editorial split-feature">
    <div class="feature-image experience" role="img" aria-label="Lido De Paris Hotel lobby courtyard"></div>
    <div class="feature-copy">
        <span class="eyebrow-title">OUR HERITAGE</span>
        <h2 class="section-title">Every stay should feel vibrant, welcoming, and memorable.</h2>
        <p>Lido De Paris Hotel &amp; Entertainment Center was founded on an enduring commitment: providing unmatched hospitality, comfortable accommodations, and memorable entertainment experiences right in the culinary and cultural center of Manila.</p>
        <p>With over 200 newly renovated rooms and suites, spacious event function halls, a relaxing wellness spa, and top-tier dining, we pair Chinese-Filipino heritage with modern conveniences for guests from around the globe.</p>
        <div style="margin-top: 2rem;">
            <a class="btn btn-outline" href="<?=url('rooms/index.php')?>">Explore Our Rooms &amp; Suites</a>
        </div>
    </div>
</section>

<section class="editorial" style="padding: 3rem 0 1rem;">
    <div style="text-align: center; max-width: 680px; margin: 0 auto 3rem;">
        <span class="eyebrow-title">OUR THREE PILLARS</span>
        <h2 class="section-title" style="margin-bottom: 0.75rem;">Crafted for your stay.</h2>
        <p>How we serve our valued guests every single day.</p>
    </div>

    <div class="stats" style="grid-template-columns: repeat(3, 1fr); gap: 2rem;">
        <div class="stat" style="padding: 2.25rem 1.75rem;">
            <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">🏮</span>
            <h3 style="font-size: 1.35rem; margin-bottom: 0.65rem;">Chinatown Prime Location</h3>
            <p style="font-size: 0.88rem; line-height: 1.7; color: var(--text-secondary, #5C625D);">
                Situated at 1036 Ongpin Street, putting you steps away from Binondo's world-famous food strip and heritage landmarks.
            </p>
        </div>
        <div class="stat" style="padding: 2.25rem 1.75rem;">
            <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">🇵🇭</span>
            <h3 style="font-size: 1.35rem; margin-bottom: 0.65rem;">Warm Hospitality</h3>
            <p style="font-size: 0.88rem; line-height: 1.7; color: var(--text-secondary, #5C625D);">
                Authentic, heartfelt care from our 24/7 front desk concierge and dedicated staff ensuring your comfort day and night.
            </p>
        </div>
        <div class="stat" style="padding: 2.25rem 1.75rem;">
            <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">🎉</span>
            <h3 style="font-size: 1.35rem; margin-bottom: 0.65rem;">Entertainment &amp; Events</h3>
            <p style="font-size: 0.88rem; line-height: 1.7; color: var(--text-secondary, #5C625D);">
                Multi-purpose event halls for celebrations, wellness spa facilities, and entertainment centers all under one roof.
            </p>
        </div>
    </div>
</section>

<section class="dark-band" style="text-align: center; margin-bottom: -5rem;">
    <span class="eyebrow-title">BEGIN YOUR JOURNEY</span>
    <h2 class="section-title" style="margin: 0.5rem auto 1.5rem; max-width: 600px;">Experience Lido De Paris Hotel for yourself.</h2>
    <p style="color: rgba(250,248,245,0.8); max-width: 500px; margin: 0 auto 2rem;">Discover our selection of rooms and suites designed for your ultimate comfort.</p>
    <a class="btn btn-gold" href="<?=url('rooms/index.php')?>">Reserve Your Room</a>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
