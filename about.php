<?php
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Our Story & Philosophy';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero" style="background-image: linear-gradient(180deg, rgba(18, 36, 28, 0.4) 0%, rgba(18, 36, 28, 0.9) 100%), url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=2000&q=85');">
    <div>
        <span class="kicker">THE JILL HOTEL STORY</span>
        <h1>Hospitality, made personal.</h1>
        <p>A serene coastal retreat honoring Filipino heritage, refined craftsmanship, and the art of unhurried travel.</p>
    </div>
</section>

<section class="editorial split-feature">
    <div class="feature-image experience" role="img" aria-label="Jill Hotel lobby courtyard"></div>
    <div class="feature-copy">
        <span class="eyebrow-title">OUR PHILOSOPHY</span>
        <h2 class="section-title">Every stay should feel memorable and deeply restful.</h2>
        <p>Jill Hotel was founded on a simple yet enduring premise: true luxury is not found in pretension, but in genuine warmth, intuitive attention to detail, and a restful sense of place.</p>
        <p>We pair world-class design standards with the world-renowned hospitality of the Philippines — ensuring that whether you visit for a brief city retreat or an extended seaside escape, you feel immediately welcomed and cared for.</p>
        <div style="margin-top: 2rem;">
            <a class="btn btn-outline" href="<?=url('rooms/index.php')?>">Explore Our Suites</a>
        </div>
    </div>
</section>

<section class="editorial" style="padding: 3rem 0 1rem;">
    <div style="text-align: center; max-width: 680px; margin: 0 auto 3rem;">
        <span class="eyebrow-title">OUR THREE PILLARS</span>
        <h2 class="section-title" style="margin-bottom: 0.75rem;">Crafted with intent.</h2>
        <p>How we approach hospitality every single day.</p>
    </div>

    <div class="stats" style="grid-template-columns: repeat(3, 1fr); gap: 2rem;">
        <div class="stat" style="padding: 2.25rem 1.75rem;">
            <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">🌿</span>
            <h3 style="font-size: 1.35rem; margin-bottom: 0.65rem;">Calm & Sanctuary</h3>
            <p style="font-size: 0.88rem; line-height: 1.7; color: var(--text-secondary, #5C625D);">
                Carefully insulated rooms, natural textures, and gentle lighting designed specifically to encourage profound rest and recharge.
            </p>
        </div>
        <div class="stat" style="padding: 2.25rem 1.75rem;">
            <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">🇵🇭</span>
            <h3 style="font-size: 1.35rem; margin-bottom: 0.65rem;">Filipino Warmth</h3>
            <p style="font-size: 0.88rem; line-height: 1.7; color: var(--text-secondary, #5C625D);">
                Authentic, heartfelt care from our dedicated concierge and housekeeping staff who anticipate needs before they are spoken.
            </p>
        </div>
        <div class="stat" style="padding: 2.25rem 1.75rem;">
            <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">✨</span>
            <h3 style="font-size: 1.35rem; margin-bottom: 0.65rem;">Thoughtful Service</h3>
            <p style="font-size: 0.88rem; line-height: 1.7; color: var(--text-secondary, #5C625D);">
                Seamless online reservations, contactless check-in convenience, flexible stays, and 24/7 personalized on-site support.
            </p>
        </div>
    </div>
</section>

<section class="dark-band" style="text-align: center; margin-bottom: -5rem;">
    <span class="eyebrow-title">BEGIN YOUR JOURNEY</span>
    <h2 class="section-title" style="margin: 0.5rem auto 1.5rem; max-width: 600px;">Experience Jill Hotel for yourself.</h2>
    <p style="color: rgba(250,248,245,0.8); max-width: 500px; margin: 0 auto 2rem;">Discover our selection of suites designed for your ultimate comfort.</p>
    <a class="btn btn-gold" href="<?=url('rooms/index.php')?>">Reserve Your Suite</a>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
