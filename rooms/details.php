<?php require __DIR__.'/../includes/bootstrap.php';$id=(int)($_GET['id']??0);$s=db()->prepare('SELECT r.*,t.name type_name FROM rooms r JOIN room_types t ON t.id=r.room_type_id WHERE r.id=?');$s->execute([$id]);$r=$s->fetch();if(!$r){require __DIR__.'/../404.php';exit;}$a=db()->prepare('SELECT a.name FROM amenities a JOIN room_amenities ra ON ra.amenity_id=a.id WHERE ra.room_id=?');$a->execute([$id]);$amenities=$a->fetchAll();$pageTitle=$r['title'];require __DIR__.'/../includes/header.php';?><div class="panel"><div class="room-photo large">ROOM <?=e($r['room_number'])?></div><p class="eyebrow"><?=e($r['type_name'])?></p><h1><?=e($r['title'])?></h1><p><?=e($r['description'])?></p><p><b class="price">₱<?=number_format($r['price_per_night'])?> / night</b> · Up to <?=$r['max_guests']?> guests</p><p><?php foreach($amenities as $x):?><span class="badge">✓ <?=e($x['name'])?></span> <?php endforeach;?></p><form action="<?=url('reservations/create.php')?>" method="post" class="booking panel"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="room_id" value="<?=$r['id']?>"><div><label>Check-in</label><input required type="date" min="<?=date('Y-m-d')?>" name="check_in" value="<?=e($_GET['check_in']??'')?>"></div><div><label>Check-out</label><input required type="date" name="check_out" value="<?=e($_GET['check_out']??'')?>"></div><div><label>Adults</label><input required type="number" min="1" name="adults" value="<?=max(1,(int)($_GET['guests']??1))?>"></div><div><label>Children</label><input type="number" min="0" name="children" value="0"></div><button>Reserve now</button></form></div><?php require __DIR__.'/../includes/footer.php'; ?>
<?php
require __DIR__ . '/../includes/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);
$s = db()->prepare('SELECT r.*, t.name AS type_name, t.description AS type_description FROM rooms r JOIN room_types t ON t.id = r.room_type_id WHERE r.id = ?');
$s->execute([$id]);
$r = $s->fetch();

if (!$r) {
    require __DIR__ . '/../404.php';
    exit;
}

$a = db()->prepare('SELECT a.name, a.description, a.icon FROM amenities a JOIN room_amenities ra ON ra.amenity_id = a.id WHERE ra.room_id = ?');
$a->execute([$id]);
$amenities = $a->fetchAll();

// Fetch room gallery images or fallback
$imgQuery = db()->prepare('SELECT image_path FROM room_images WHERE room_id = ? ORDER BY is_primary DESC, id ASC');
$imgQuery->execute([$id]);
$dbImages = $imgQuery->fetchAll(PDO::FETCH_COLUMN);

$galleryPool = [
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=85',
    'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=85'
];

$images = !empty($dbImages) ? $dbImages : $galleryPool;
$primaryImage = $images[0];

$checkIn = $_GET['check_in'] ?? '';
$checkOut = $_GET['check_out'] ?? '';
$adults = max(1, (int)($_GET['guests'] ?? 1));

$pageTitle = $r['title'] . ' — Suite ' . $r['room_number'];
require __DIR__ . '/../includes/header.php';
?>

<div style="margin-bottom: 2rem;">
    <a href="<?=url('rooms/index.php')?>" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; display: inline-flex; align-items: center; gap: 0.35rem;">
        &larr; Back to all suites
    </a>
</div>

<div class="room-details-grid">
    <div class="room-main-content">
        <section class="room-gallery" aria-label="Room gallery">
            <div id="main-photo" class="primary-photo" style="background-image: url('<?=e($primaryImage)?>');" role="img" aria-label="<?=e($r['title'])?>">
                <button class="favorite-button" type="button" data-favorite="<?=$r['id']?>" aria-label="Save to favorites">♡</button>
            </div>
            <?php if (count($images) > 1): ?>
                <div class="photo-strip" role="group" aria-label="Gallery thumbnails">
                    <?php foreach (array_slice($images, 0, 3) as $thumb): ?>
                        <div style="background-image: url('<?=e($thumb)?>');" onclick="document.getElementById('main-photo').style.backgroundImage='url(\'<?=e(addslashes($thumb))?>\')'" role="button" tabindex="0" aria-label="View photo"></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <div>
            <span class="kicker"><?=e($r['type_name'])?> &middot; Room <?=e($r['room_number'])?></span>
            <h1 style="font-size: clamp(2.2rem, 3.5vw, 3rem); margin-bottom: 0.75rem;"><?=e($r['title'])?></h1>
            <p style="font-size: 1.05rem; line-height: 1.8; color: var(--text-secondary, #5C625D); margin-bottom: 1.5rem;">
                <?=nl2br(e($r['description']))?>
            </p>
        </div>

        <section class="room-spec-bar" aria-label="Room specifications">
            <div class="room-spec-item">
                <span>Capacity</span>
                <strong>Up to <?=$r['max_guests']?> Guests</strong>
            </div>
            <div class="room-spec-item">
                <span>Bed Type</span>
                <strong><?=e($r['bed_type'] ?? 'King Bed')?></strong>
            </div>
            <div class="room-spec-item">
                <span>Room Size</span>
                <strong><?=e($r['size'] ?? '35 sqm')?></strong>
            </div>
            <div class="room-spec-item">
                <span>Location</span>
                <strong>Floor <?=e($r['floor'] ?? '2')?></strong>
            </div>
            <div class="room-spec-item">
                <span>Status</span>
                <span class="badge" data-status="<?=e($r['status'])?>"><?=e($r['status'])?></span>
            </div>
        </section>

        <section aria-labelledby="amenities-heading">
            <h2 id="amenities-heading" style="font-size: 1.5rem; margin-bottom: 0.75rem;">Suite Amenities & Inclusions</h2>
            <div class="amenities-tag-cloud">
                <?php foreach ($amenities as $am): ?>
                    <span class="amenity-chip">
                        <span aria-hidden="true" style="color: var(--accent, #B89650);">✦</span>
                        <?=e($am['name'])?>
                    </span>
                <?php endforeach; ?>
                <span class="amenity-chip"><span aria-hidden="true" style="color: var(--accent, #B89650);">✦</span> Complimentary High-Speed Wi-Fi</span>
                <span class="amenity-chip"><span aria-hidden="true" style="color: var(--accent, #B89650);">✦</span> Daily Housekeeping</span>
                <span class="amenity-chip"><span aria-hidden="true" style="color: var(--accent, #B89650);">✦</span> In-Room Espresso & Tea</span>
            </div>
        </section>

        <section class="panel" style="background-color: var(--surface-muted, #F4EFE6); border: none; margin-top: 1rem;">
            <h3 style="font-size: 1.15rem; margin-bottom: 0.5rem;">Stay Policies & Information</h3>
            <p style="font-size: 0.88rem; line-height: 1.6; margin-bottom: 0.5rem;">
                <strong>Check-in:</strong> 3:00 PM &middot; <strong>Check-out:</strong> 12:00 PM
            </p>
            <p style="font-size: 0.85rem; color: var(--text-secondary, #5C625D); line-height: 1.6;">
                Flexible cancellation up to 24 hours prior to arrival date. No prepayment needed at booking time — payment is completed securely at reception upon arrival.
            </p>
        </section>
    </div>

    <aside>
        <div class="booking-sidebar-card">
            <div class="booking-rate-header">
                <div>
                    <span style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-secondary, #5C625D);">Nightly Rate</span>
                    <div class="rate">₱<?=number_format((float)$r['price_per_night'])?><small> / night</small></div>
                </div>
                <span class="badge success">Best Rate Guarantee</span>
            </div>

            <form action="<?=url('reservations/create.php')?>" method="post">
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <input type="hidden" name="room_id" value="<?=$r['id']?>">

                <div style="margin-bottom: 1rem;">
                    <label for="res-checkin">Check-in Date</label>
                    <input required id="res-checkin" type="date" min="<?=date('Y-m-d')?>" name="check_in" value="<?=e($checkIn)?>">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="res-checkout">Check-out Date</label>
                    <input required id="res-checkout" type="date" min="<?=date('Y-m-d', strtotime('+1 day'))?>" name="check_out" value="<?=e($checkOut)?>">
                </div>

                <div class="form-grid" style="margin-bottom: 1rem;">
                    <div>
                        <label for="res-adults">Adults</label>
                        <input required id="res-adults" type="number" min="1" max="<?=$r['max_guests']?>" name="adults" value="<?=$adults?>">
                    </div>
                    <div>
                        <label for="res-children">Children</label>
                        <input id="res-children" type="number" min="0" max="4" name="children" value="0">
                    </div>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="res-requests">Special Requests (Optional)</label>
                    <textarea id="res-requests" name="special_requests" placeholder="Early check-in, high floor, quiet room, etc." style="min-height: 70px;"></textarea>
                </div>

                <div style="background: var(--surface-muted, #F4EFE6); border-radius: 4px; padding: 0.85rem 1rem; margin-bottom: 1rem; font-size: 0.82rem; color: var(--text-secondary, #5C625D);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                        <span>Taxes & Service Charge:</span>
                        <span>12% included</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Payment:</span>
                        <strong style="color: var(--text-primary, #2A2D2A);">Pay at Hotel (Cash / GCash / Card)</strong>
                    </div>
                </div>

                <button class="btn btn-gold" type="submit">Reserve This Suite</button>
            </form>
        </div>
    </aside>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
