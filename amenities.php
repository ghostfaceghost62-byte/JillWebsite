<?php
require __DIR__ . '/includes/bootstrap.php';

$rows = db()->query('SELECT * FROM amenities ORDER BY id')->fetchAll();
$pageTitle = 'Amenities & Guest Services';
require __DIR__ . '/includes/header.php';

$amenityVisuals = [
    1 => 'https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=1200&q=85', // Wi-Fi / workspace
    2 => 'https://images.unsplash.com/photo-1572331165267-854da2b10ccc?auto=format&fit=crop&w=1200&q=85', // Pool
    3 => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=85', // AC / Comfort
    4 => 'https://images.unsplash.com/photo-1506521781263-d8422e82f27a?auto=format&fit=crop&w=1200&q=85', // Parking
    5 => 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?auto=format&fit=crop&w=1200&q=85', // Breakfast
    6 => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?auto=format&fit=crop&w=1200&q=85', // Gym
    7 => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=85', // Restaurant
    8 => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=85', // Room service
    9 => 'https://images.unsplash.com/photo-1593784991095-a205069470b6?auto=format&fit=crop&w=1200&q=85', // Smart TV
    10 => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1200&q=85', // Balcony
];
?>

<section class="page-hero" style="background-image: linear-gradient(180deg, rgba(18, 36, 28, 0.4) 0%, rgba(18, 36, 28, 0.9) 100%), url('https://images.unsplash.com/photo-1572331165267-854da2b10ccc?auto=format&fit=crop&w=2000&q=85');">
    <div>
        <span class="kicker">HOTEL SERVICES & EXPERIENCES</span>
        <h1>Curated for pure comfort.</h1>
        <p>From sunset swims to locally roasted morning coffee, every amenity at Lido De Paris Hotel is designed to elevate your stay.</p>
    </div>
</section>

<section class="editorial" style="padding-top: 1rem;">
    <?php foreach ($rows as $index => $a):
        $visual = $amenityVisuals[$a['id']] ?? 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=85';
    ?>
        <article class="amenity-editorial">
            <div class="amenity-visual" style="background-image: url('<?=e($visual)?>');" role="img" aria-label="<?=e($a['name'])?>"></div>
            <div>
                <span class="eyebrow-title">FEATURED SERVICE 0<?=($index + 1)?></span>
                <h2 class="section-title" style="margin-bottom: 1rem;"><?=e($a['name'])?></h2>
                <p style="font-size: 1.05rem; line-height: 1.8; color: var(--text-secondary, #5C625D); margin-bottom: 1.25rem;">
                    <?=e($a['description'])?>
                </p>
                <p style="font-size: 0.9rem; color: var(--text-secondary, #7A807B); line-height: 1.7;">
                    Included as part of your stay with Lido De Paris Hotel. Our dedicated team is available 24 hours a day to assist with any bespoke arrangements.
                </p>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<section class="dark-band" style="text-align: center; margin-bottom: -5rem;">
    <span class="eyebrow-title">PLAN YOUR VISIT</span>
    <h2 class="section-title" style="margin: 0.5rem auto 1.75rem; max-width: 600px;">Ready to experience our sanctuary?</h2>
    <a class="btn btn-gold" href="<?=url('rooms/index.php')?>">Check Room Availability</a>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
