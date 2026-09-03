<?php
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Contact Concierge & Location';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero" style="background-image: linear-gradient(180deg, rgba(18, 36, 28, 0.4) 0%, rgba(18, 36, 28, 0.9) 100%), url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=2000&q=85');">
    <div>
        <span class="kicker">GET IN TOUCH</span>
        <h1>We are here for you.</h1>
        <p>Whether planning your journey, arranging airport transfers, or inquiring about special stays, our concierge team is on hand 24 hours a day.</p>
    </div>
</section>

<div class="stats" style="grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-bottom: 3.5rem;">
    <div class="stat" style="padding: 2.25rem 1.75rem;">
        <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">📍</span>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Our Location</h3>
        <p style="font-size: 0.92rem; line-height: 1.7; color: var(--text-secondary, #5C625D);">
            101 Seaside Avenue<br>
            Manila, Metro Manila<br>
            Philippines
        </p>
        <p style="font-size: 0.8rem; color: var(--accent, #B89650); font-weight: 600; margin-top: 0.5rem;">
            Complimentary on-site valet parking
        </p>
    </div>

    <div class="stat" style="padding: 2.25rem 1.75rem;">
        <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">📞</span>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Direct Inquiries</h3>
        <p style="font-size: 0.92rem; line-height: 1.7; color: var(--text-secondary, #5C625D);">
            Phone: <strong>+63 2 8123 4567</strong><br>
            Mobile: +63 900 000 0000<br>
            Email: <a href="mailto:stay@hotelreserve.local">stay@hotelreserve.local</a>
        </p>
        <p style="font-size: 0.8rem; color: var(--accent, #B89650); font-weight: 600; margin-top: 0.5rem;">
            24/7 Front Desk & Concierge
        </p>
    </div>

    <div class="stat" style="padding: 2.25rem 1.75rem;">
        <span style="font-size: 1.5rem; display: block; margin-bottom: 0.75rem;">⏰</span>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Arrival & Departure</h3>
        <p style="font-size: 0.92rem; line-height: 1.7; color: var(--text-secondary, #5C625D);">
            Check-in time: <strong>3:00 PM</strong><br>
            Check-out time: <strong>12:00 PM</strong><br>
            Early check-in upon request
        </p>
        <p style="font-size: 0.8rem; color: var(--accent, #B89650); font-weight: 600; margin-top: 0.5rem;">
            Luggage storage available
        </p>
    </div>
</div>

<section class="panel" style="max-width: 800px; margin: 0 auto; text-align: center; padding: 3rem 2rem;">
    <span class="eyebrow-title">PLAN YOUR ARRIVAL</span>
    <h2 style="font-size: 1.85rem; margin-bottom: 1rem;">Looking for your next stay?</h2>
    <p style="max-width: 540px; margin: 0 auto 2rem; color: var(--text-secondary, #5C625D);">
        Explore our curated collection of rooms and suites designed for effortless relaxation.
    </p>
    <a class="btn btn-gold" href="<?=url('rooms/index.php')?>">Browse Available Suites</a>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
