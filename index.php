<?php
require __DIR__ . '/includes/bootstrap.php';

$types = db()->query('SELECT id, name FROM room_types ORDER BY name')->fetchAll();
$rooms = db()->query("
    SELECT r.*, t.name AS type_name,
           (SELECT image_path FROM room_images ri WHERE ri.room_id = r.id ORDER BY ri.is_primary DESC, ri.id LIMIT 1) AS image_path
    FROM rooms r
    JOIN room_types t ON t.id = r.room_type_id
    WHERE r.featured = 1 AND r.status = 'AVAILABLE'
    ORDER BY r.id
    LIMIT 3
")->fetchAll();
$amenities = db()->query('SELECT name, description FROM amenities ORDER BY id LIMIT 4')->fetchAll();
$pageTitle = 'Luxury Boutique Hotel Manila';
require __DIR__ . '/includes/header.php';

$fallbackImages = [
    'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=85',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=85',
    'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=85'
];
?>

<section class="hero-luxury" aria-label="Welcome Hero">
    <div class="hero-copy">
        <span class="kicker">WELCOME TO JILL HOTEL MANILA</span>
        <h1>Your stay,<br>elevated.</h1>
        <p>A tranquil boutique sanctuary blending warm Filipino hospitality with refined, understated luxury along Seaside Avenue.</p>
        <div class="hero-actions">
            <a class="btn btn-gold" href="#book">Book your stay</a>
            <a class="btn btn-outline" href="<?=url('rooms/index.php')?>">Explore suites</a>
        </div>
    </div>
</section>

<form id="book" class="booking-widget" action="<?=url('rooms/index.php')?>" method="get" aria-label="Quick room reservation">
    <div>
        <label for="widget-checkin">Check-in</label>
        <input required id="widget-checkin" type="date" min="<?=date('Y-m-d')?>" name="check_in">
    </div>
    <div>
        <label for="widget-checkout">Check-out</label>
        <input required id="widget-checkout" type="date" min="<?=date('Y-m-d', strtotime('+1 day'))?>" name="check_out">
    </div>
    <div>
        <label for="widget-guests">Guests</label>
        <select id="widget-guests" name="guests">
            <?php for ($g = 1; $g <= 8; $g++): ?>
                <option value="<?=$g?>" <?=$g === 2 ? 'selected' : ''?>><?=$g?> Guest<?=$g > 1 ? 's' : ''?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div>
        <label for="widget-type">Room type</label>
        <select id="widget-type" name="type">
            <option value="">All Rooms &amp; Suites</option>
            <?php foreach ($types as $t): ?>
                <option value="<?=$t['id']?>"><?=e($t['name'])?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button class="btn btn-gold" type="submit">Check Availability</button>
</form>

<section class="editorial split-feature" aria-labelledby="experience-title">
    <div class="feature-image experience" role="img" aria-label="Jill Hotel luxury lobby lounge"></div>
    <div class="feature-copy">
        <span class="eyebrow-title">THE JILL HOTEL EXPERIENCE</span>
        <h2 id="experience-title" class="section-title">A quieter kind of luxury in the heart of Manila.</h2>
        <p>From the serene arrival on Seaside Avenue to the thoughtful calm of each private suite, every element has been curated for an unhurried, restorative stay.</p>
        <p>Whether visiting for a restful coastal getaway or executive business, our dedicated concierge ensures every detail feels effortless and distinctly yours.</p>
        <a class="btn btn-outline" href="<?=url('about.php')?>">Discover Our Story</a>
    </div>
</section>

<section class="editorial" aria-labelledby="showcase-title">
    <span class="eyebrow-title">OUR ROOMS &amp; SUITES</span>
    <h2 id="showcase-title" class="section-title">Designed for deep rest and memorable stays.</h2>
    <div class="room-showcase">
        <?php foreach ($rooms as $idx => $r):
            $imgUrl = !empty($r['image_path']) ? $r['image_path'] : ($fallbackImages[$idx % count($fallbackImages)]);
        ?>
            <article class="luxury-room">
                <div class="room-photo" style="background-image: linear-gradient(180deg, transparent 40%, rgba(18, 36, 28, 0.75) 100%), url('<?=e($imgUrl)?>');" role="img" aria-label="<?=e($r['title'])?>">
                    <span>Room <?=e($r['room_number'])?></span>
                    <button class="favorite-button" type="button" data-favorite="<?=$r['id']?>" aria-label="Save <?=e($r['title'])?> to favorites">&#9825;</button>
                </div>
                <div class="room-body">
                    <span class="kicker"><?=e($r['type_name'])?></span>
                    <h3><?=e($r['title'])?></h3>
                    <p class="room-desc"><?=e($r['description'])?></p>
                    <div class="room-meta">
                        <?=e($r['size'] ?? '35 sqm')?> &middot; Up to <?=$r['max_guests']?> guests &middot; <?=e($r['bed_type'] ?? 'King Bed')?>
                    </div>
                    <div class="room-footer">
                        <div class="price">
                            From ₱<?=number_format((float)$r['price_per_night'])?>
                            <small>per night</small>
                        </div>
                        <a class="btn small" href="<?=url('rooms/details.php?id=' . $r['id'])?>">View Suite</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="dark-band" aria-labelledby="amenities-band-title">
    <span class="eyebrow-title">EXCLUSIVE SERVICES</span>
    <h2 id="amenities-band-title" class="section-title">Everything you need, thoughtfully arranged.</h2>
    <div class="amenity-list">
        <?php foreach ($amenities as $a): ?>
            <article>
                <h3><?=e($a['name'])?></h3>
                <p><?=e($a['description'])?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="editorial" aria-labelledby="gallery-title">
    <span class="eyebrow-title">A SENSE OF PLACE</span>
    <h2 id="gallery-title" class="section-title">Moments worth lingering over.</h2>
    <div class="gallery-grid" aria-label="Hotel photo gallery">
        <div role="img" aria-label="Scenic infinity pool overlooking the bay"></div>
        <div role="img" aria-label="Signature dining restaurant"></div>
        <div role="img" aria-label="Peaceful wellness spa suite"></div>
        <div role="img" aria-label="Evening terrace ambiance"></div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
