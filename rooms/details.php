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

// Fetch Reviews
$revStmt = db()->prepare('
    SELECT rv.*, u.first_name, u.last_name 
    FROM reviews rv 
    JOIN users u ON u.id = rv.user_id 
    WHERE rv.room_id = ? 
    ORDER BY rv.created_at DESC
');
$revStmt->execute([$id]);
$reviews = $revStmt->fetchAll();

$avgRating = 0;
$totalReviews = count($reviews);
if ($totalReviews > 0) {
    $sum = 0;
    foreach ($reviews as $rev) {
        $sum += $rev['rating'];
    }
    $avgRating = round($sum / $totalReviews, 1);
}

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

// Fetch active add-ons
$addOnsStmt = db()->query('SELECT * FROM add_ons WHERE active = 1 ORDER BY id');
$activeAddOns = $addOnsStmt->fetchAll();

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

        <section class="room-reviews" style="margin-top: 2rem;">
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">
                Guest Reviews
                <?php if ($totalReviews > 0): ?>
                    <span style="font-size: 1rem; font-weight: normal; margin-left: 1rem;">
                        <span style="color: #f1c40f;">★</span> <?=number_format($avgRating, 1)?> (<?=$totalReviews?> review<?=$totalReviews>1?'s':''?>)
                    </span>
                <?php endif; ?>
            </h2>

            <?php if ($totalReviews > 0): ?>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach ($reviews as $rev): ?>
                        <div class="review-card" style="background: var(--surface, #fff); padding: 1.25rem; border: 1px solid var(--border); border-radius: 8px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <strong><?=e($rev['first_name'] . ' ' . substr($rev['last_name'], 0, 1) . '.')?></strong>
                                <span style="color: var(--text-secondary); font-size: 0.8rem;"><?=date('M j, Y', strtotime($rev['created_at']))?></span>
                            </div>
                            <div style="color: #f1c40f; margin-bottom: 0.5rem; font-size: 0.9rem;">
                                <?=str_repeat('★', (int)$rev['rating'])?><?=str_repeat('☆', 5 - (int)$rev['rating'])?>
                            </div>
                            <?php if (!empty($rev['title'])): ?>
                                <h4 style="font-size: 0.95rem; margin-bottom: 0.25rem; font-weight: 600;"><?=e($rev['title'])?></h4>
                            <?php endif; ?>
                            <p style="font-size: 0.9rem; line-height: 1.5; color: var(--text-secondary); margin: 0;">
                                <?=nl2br(e($rev['comment']))?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--text-secondary);">No reviews yet. Be the first to share your experience after your stay!</p>
            <?php endif; ?>
        </section>
    </div>

    <aside>
        <div class="booking-sidebar-card">
            <div class="booking-rate-header">
                <div>
                    <span style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-secondary, #5C625D);">Nightly Rate</span>
                    <div class="rate"><span data-php-price="<?=(float)$r['price_per_night']?>">₱<?=number_format((float)$r['price_per_night'], 2)?></span><small> / night</small></div>
                </div>
                <span class="badge success">Best Rate Guarantee</span>
            </div>

            <form action="<?=url('reservations/create.php')?>" method="post">
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <input type="hidden" name="room_id" value="<?=$r['id']?>">

                <div style="margin-bottom: 1rem;">
                    <label for="res-checkin">Check-in Date</label>
                    <input required id="res-checkin" class="date-picker-input" type="text" name="check_in" value="<?=e($checkIn)?>" placeholder="Select date" readonly>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label for="res-checkout">Check-out Date</label>
                    <input required id="res-checkout" class="date-picker-input" type="text" name="check_out" value="<?=e($checkOut)?>" placeholder="Select date" readonly>
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

                <?php if ($activeAddOns): ?>
                <div style="margin-bottom: 1.25rem;">
                    <label>Enhance Your Stay (Optional)</label>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                        <?php foreach ($activeAddOns as $addon): ?>
                            <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer; font-size: 0.85rem; line-height: 1.4;">
                                <input type="checkbox" name="addons[]" value="<?=$addon['id']?>" style="margin-top: 0.2rem;">
                                <div>
                                    <strong style="display: block; color: var(--text-primary);"><?=e($addon['name'])?> <span style="color: var(--brand);">(+<span data-php-price="<?=(float)$addon['price']?>">₱<?=number_format((float)$addon['price'], 2)?></span>)</span></strong>
                                    <span style="color: var(--text-secondary); font-size: 0.8rem;"><?=e($addon['description'])?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.date-picker-input { background: var(--surface); border: 1px solid var(--border); padding: 0.65rem; width: 100%; border-radius: 4px; color: var(--text-primary); cursor: pointer; }
.flatpickr-day.disabled { color: #ff4757 !important; text-decoration: line-through; }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const roomId = <?=$r['id']?>;
    const inInput = document.getElementById('res-checkin');
    const outInput = document.getElementById('res-checkout');
    let blockedDates = [];

    fetch('<?=url('api/availability.php?room_id=')?>' + roomId)
        .then(res => res.json())
        .then(dates => {
            blockedDates = dates;
            initPickers();
        })
        .catch(() => initPickers()); // fallback

    function initPickers() {
        flatpickr(inInput, {
            minDate: "today",
            disable: blockedDates,
            onChange: function(selectedDates, dateStr, instance) {
                outPicker.set("minDate", dateStr ? new Date(selectedDates[0].getTime() + 86400000) : "today");
                // Check if any blocked dates fall between check-in and check-out
                if (outInput.value && dateStr) {
                    let cIn = selectedDates[0];
                    let cOut = outPicker.selectedDates[0];
                    let valid = true;
                    if (cOut && cOut > cIn) {
                        for (let d = new Date(cIn); d < cOut; d.setDate(d.getDate() + 1)) {
                            let fDate = d.toISOString().split('T')[0];
                            if (blockedDates.includes(fDate)) valid = false;
                        }
                    }
                    if (!valid) {
                        alert("Your selected date range includes unavailable dates.");
                        outPicker.clear();
                    }
                }
            }
        });

        const outPicker = flatpickr(outInput, {
            minDate: inInput.value ? new Date(new Date(inInput.value).getTime() + 86400000) : new Date(new Date().getTime() + 86400000),
            disable: blockedDates,
            onChange: function(selectedDates, dateStr, instance) {
                if (inInput.value && dateStr) {
                    let cIn = new Date(inInput.value);
                    let cOut = selectedDates[0];
                    let valid = true;
                    if (cOut > cIn) {
                        for (let d = new Date(cIn); d < cOut; d.setDate(d.getDate() + 1)) {
                            let fDate = d.toISOString().split('T')[0];
                            if (blockedDates.includes(fDate)) valid = false;
                        }
                    }
                    if (!valid) {
                        alert("Your selected date range includes unavailable dates.");
                        instance.clear();
                    }
                }
            }
        });
    }
});
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
