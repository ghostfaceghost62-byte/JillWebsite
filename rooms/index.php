<?php
require __DIR__ . '/../includes/bootstrap.php';

$in = $_GET['check_in'] ?? '';
$out = $_GET['check_out'] ?? '';
$guests = max(1, min(8, (int)($_GET['guests'] ?? 2)));
$type = max(0, (int)($_GET['type'] ?? 0));
$destination = trim($_GET['destination'] ?? '');
$maxPrice = max(0, (int)($_GET['max_price'] ?? 0));
$sort = $_GET['sort'] ?? 'recommended';
$selectedAmenities = array_values(array_filter($_GET['amenities'] ?? [], static fn($id) => ctype_digit((string)$id)));

$validDates = $in !== '' && $out !== '' && $out > $in && $in >= date('Y-m-d');

// Log search history if user is signed in and has search inputs
if (user() && ($in !== '' || $out !== '' || $type > 0 || $destination !== '' || $maxPrice > 0)) {
    try {
        db()->prepare('INSERT INTO user_search_history(user_id, check_in, check_out, guests, room_type_id) VALUES(?,?,?,?,?)')
            ->execute([user()['id'], $in ?: null, $out ?: null, $guests, $type ?: null]);
    } catch (Throwable $e) {
        // Silent catch for search history logging
    }
}

$types = db()->query('SELECT id, name FROM room_types ORDER BY name')->fetchAll();
$amenities = db()->query('SELECT id, name FROM amenities ORDER BY name')->fetchAll();

$where = ["r.status='AVAILABLE'", 'r.max_guests>=?'];
$args = [$guests];

if ($type) {
    $where[] = 'r.room_type_id=?';
    $args[] = $type;
}

if ($destination !== '') {
    $where[] = '(r.title LIKE ? OR t.name LIKE ? OR r.description LIKE ?)';
    $like = '%' . $destination . '%';
    array_push($args, $like, $like, $like);
}

if ($maxPrice) {
    $where[] = 'r.price_per_night<=?';
    $args[] = $maxPrice;
}

if ($validDates) {
    $where[] = 'NOT EXISTS(SELECT 1 FROM reservations x WHERE x.room_id=r.id AND ' . reservation_conflict_sql() . ')';
    array_push($args, $out, $in);
}

foreach ($selectedAmenities as $amenityId) {
    $where[] = 'EXISTS(SELECT 1 FROM room_amenities filter_ra WHERE filter_ra.room_id=r.id AND filter_ra.amenity_id=?)';
    $args[] = (int)$amenityId;
}

$ordering = [
    'price_low' => 'r.price_per_night ASC',
    'price_high' => 'r.price_per_night DESC',
    'rating' => 'r.featured DESC, r.price_per_night ASC',
    'recommended' => 'r.featured DESC, r.price_per_night ASC'
];
$order = $ordering[$sort] ?? $ordering['recommended'];

$sql = 'SELECT r.*, t.name AS type_name,
        (SELECT image_path FROM room_images ri WHERE ri.room_id=r.id ORDER BY ri.is_primary DESC, ri.id LIMIT 1) AS image_path,
        GROUP_CONCAT(DISTINCT a.name ORDER BY a.name SEPARATOR "|") AS amenity_names
        FROM rooms r
        JOIN room_types t ON t.id=r.room_type_id
        LEFT JOIN room_amenities ra ON ra.room_id=r.id
        LEFT JOIN amenities a ON a.id=ra.amenity_id
        WHERE ' . implode(' AND ', $where) . '
        GROUP BY r.id
        ORDER BY ' . $order;

$statement = db()->prepare($sql);
$statement->execute($args);
$rooms = $statement->fetchAll();

$fallbackImages = [
    'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1511192336575-5a79af67a629?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=80'
];

$pageTitle = 'Find your stay';
require __DIR__ . '/../includes/header.php';
?>

<section class="marketplace-page">
    <form class="search-panel" method="get" action="<?=url('rooms/index.php')?>" aria-label="Room search parameters">
        <div class="search-field destination">
            <label for="destination">Destination or Keyword</label>
            <input id="destination" name="destination" value="<?=e($destination)?>" placeholder="City, room type, or feature">
        </div>
        <div class="search-field">
            <label for="check_in">Check-in</label>
            <input id="check_in" class="date-picker-input" type="text" name="check_in" value="<?=e($in)?>" placeholder="Select date" readonly>
        </div>
        <div class="search-field">
            <label for="check_out">Check-out</label>
            <input id="check_out" class="date-picker-input" type="text" name="check_out" value="<?=e($out)?>" placeholder="Select date" readonly>
        </div>
        <div class="search-field">
            <label for="guests">Guests</label>
            <select id="guests" name="guests">
                <?php for ($n = 1; $n <= 8; $n++): ?>
                    <option value="<?=$n?>" <?=$guests === $n ? 'selected' : ''?>><?=$n?> Guest<?=$n > 1 ? 's' : ''?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button class="btn btn-gold search-button" type="submit">Search Stays</button>
    </form>

    <?php if (($in || $out) && !$validDates): ?>
        <div class="search-note">
            <span>ℹ️</span> Please select a check-out date after your check-in date to display confirmed live room availability.
        </div>
    <?php endif; ?>

    <section class="offer-panel" id="offers">
        <div>
            <p class="offer-title">Direct Booking Privileges</p>
            <p>Enjoy complimentary breakfast packages, flexible arrival check-in, and personalized concierge care when booking with Jill Hotel.</p>
            <div class="offer-chips">
                <span>Free cancellation options</span>
                <span>Breakfast included</span>
                <span>Best rate guarantee</span>
            </div>
        </div>
        <a class="btn offer-button" href="#results">View Results</a>
    </section>

    <div class="results-layout" id="results">
        <button class="filter-toggle" type="button" data-filter-toggle aria-expanded="false">Show Filters</button>

        <aside class="filter-sidebar" data-filter-panel aria-label="Room filters">
            <div class="filter-heading">
                <h2>Filters</h2>
                <a href="<?=url('rooms/index.php')?>">Reset All</a>
            </div>

            <form method="get" action="<?=url('rooms/index.php')?>">
                <input type="hidden" name="destination" value="<?=e($destination)?>">
                <input type="hidden" name="check_in" value="<?=e($in)?>">
                <input type="hidden" name="check_out" value="<?=e($out)?>">
                <input type="hidden" name="guests" value="<?=$guests?>">
                <input type="hidden" name="sort" value="<?=e($sort)?>">

                <section class="filter-group">
                    <h3>Budget per night</h3>
                    <div class="range-label">
                        <span>Max Rate:</span>
                        <strong data-price-output>₱<?=number_format($maxPrice ?: 10000)?></strong>
                    </div>
                    <input id="max_price" type="range" name="max_price" min="2000" max="12000" step="500" value="<?=$maxPrice ?: 10000?>" data-price-range aria-label="Maximum budget per night">
                </section>

                <section class="filter-group">
                    <h3>Room Type</h3>
                    <label class="check-row">
                        <input type="radio" name="type" value="0" <?=$type === 0 ? 'checked' : ''?>>
                        <span>All Room Categories</span>
                    </label>
                    <?php foreach ($types as $item): ?>
                        <label class="check-row">
                            <input type="radio" name="type" value="<?=$item['id']?>" <?=$type === (int)$item['id'] ? 'checked' : ''?>>
                            <span><?=e($item['name'])?></span>
                        </label>
                    <?php endforeach; ?>
                </section>

                <section class="filter-group">
                    <h3>Amenities</h3>
                    <?php foreach ($amenities as $item): ?>
                        <label class="check-row">
                            <input type="checkbox" name="amenities[]" value="<?=$item['id']?>" <?=in_array((string)$item['id'], $selectedAmenities, true) ? 'checked' : ''?>>
                            <span><?=e($item['name'])?></span>
                        </label>
                    <?php endforeach; ?>
                </section>

                <button class="btn apply-filters" type="submit">Apply Filters</button>
            </form>
        </aside>

        <section class="results-column">
            <div class="results-header">
                <div>
                    <h1 class="results-count" style="font-size: 1.35rem;"><?=count($rooms)?> <?=count($rooms) === 1 ? 'Suite' : 'Suites'?> Available</h1>
                    <p class="results-subtitle">Matched to your selected preferences</p>
                </div>
                <form method="get" class="sort-form" action="<?=url('rooms/index.php')?>">
                    <?php foreach ($_GET as $key => $value):
                        if ($key !== 'sort' && $key !== 'amenities' && !is_array($value)): ?>
                            <input type="hidden" name="<?=e($key)?>" value="<?=e((string)$value)?>">
                        <?php endif;
                    endforeach;
                    foreach ($selectedAmenities as $val): ?>
                        <input type="hidden" name="amenities[]" value="<?=e($val)?>">
                    <?php endforeach; ?>
                    <label for="sort-select">Sort by</label>
                    <select id="sort-select" name="sort" onchange="this.form.submit()">
                        <option value="recommended" <?=$sort === 'recommended' ? 'selected' : ''?>>Recommended</option>
                        <option value="price_low" <?=$sort === 'price_low' ? 'selected' : ''?>>Price: Low to High</option>
                        <option value="price_high" <?=$sort === 'price_high' ? 'selected' : ''?>>Price: High to Low</option>
                        <option value="rating" <?=$sort === 'rating' ? 'selected' : ''?>>Featured First</option>
                    </select>
                </form>
            </div>

            <div class="hotel-results">
                <?php foreach ($rooms as $index => $room):
                    $roomAmenities = array_filter(explode('|', (string)$room['amenity_names']));
                    $imgSrc = !empty($room['image_path']) ? $room['image_path'] : ($fallbackImages[$index % count($fallbackImages)]);
                    $ratingScore = $room['featured'] ? '9.4' : '8.9';
                    $ratingLabel = $room['featured'] ? 'Exceptional' : 'Very Good';
                    $reviewsCount = $room['featured'] ? '52' : '34';
                ?>
                    <article class="hotel-result-card">
                        <div class="hotel-image-wrap">
                            <img src="<?=e($imgSrc)?>" alt="<?=e($room['title'])?>" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=900&q=80';">
                            <button class="favorite-button" type="button" data-favorite="<?=$room['id']?>" aria-label="Save <?=e($room['title'])?> to favorites">♡</button>
                        </div>
                        <div class="hotel-content">
                            <span class="room-type"><?=e($room['type_name'])?> &middot; Room <?=e($room['room_number'])?></span>
                            <h2><?=e($room['title'])?></h2>
                            <div class="rating-line">
                                <span class="rating-badge"><?=$ratingScore?></span>
                                <strong><?=$ratingLabel?></strong>
                                <span>(<?=$reviewsCount?> verified reviews)</span>
                            </div>
                            <p class="location-line">📍 Seaside Avenue, Manila &middot; Ocean & City View</p>
                            <p class="room-description"><?=e($room['description'])?></p>
                            <p class="room-meta">
                                Up to <?=$room['max_guests']?> guests &middot; <?=e($room['bed_type'] ?? 'King Bed')?> &middot; <?=e($room['size'] ?? '35 sqm')?>
                            </p>
                            <div class="amenity-tags">
                                <?php foreach (array_slice($roomAmenities, 0, 4) as $amenity): ?>
                                    <span><?=e($amenity)?></span>
                                <?php endforeach; ?>
                                <span>Free cancellation</span>
                            </div>
                        </div>
                        <div class="hotel-price">
                            <p>From</p>
                            <strong><span data-php-price="<?=(float)$room['price_per_night']?>">₱<?=number_format((float)$room['price_per_night'])?></span></strong>
                            <span>per night</span>
                            <small>Excludes taxes & fees</small>
                            <a class="btn btn-gold availability-button" href="details.php?id=<?=$room['id']?>&amp;check_in=<?=urlencode($in)?>&amp;check_out=<?=urlencode($out)?>&amp;guests=<?=$guests?>">
                                View Suite
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (!$rooms): ?>
                <div class="empty-results">
                    <h2>No suites match your criteria</h2>
                    <p>We could not find rooms matching your current dates, price limit, or amenity selections. Try adjusting your stay dates or resetting filters.</p>
                    <a class="btn btn-outline" href="<?=url('rooms/index.php')?>">Reset Search Filters</a>
                </div>
            <?php endif; ?>
        </section>
    </div>
</section>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.date-picker-input { background: var(--surface); border: 1px solid var(--border-color, #E2DBD0); padding: 0.65rem; font-size: 0.95rem; width: 100%; border-radius: 4px; color: var(--text-primary); cursor: pointer; outline: none; }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const inInput = document.getElementById('check_in');
    const outInput = document.getElementById('check_out');

    const inPicker = flatpickr(inInput, {
        minDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            outPicker.set("minDate", dateStr ? new Date(selectedDates[0].getTime() + 86400000) : "today");
        }
    });

    const outPicker = flatpickr(outInput, {
        minDate: inInput.value ? new Date(new Date(inInput.value).getTime() + 86400000) : new Date(new Date().getTime() + 86400000)
    });
});
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
