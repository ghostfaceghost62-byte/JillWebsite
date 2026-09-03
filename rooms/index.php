<?php require __DIR__.'/../includes/bootstrap.php';$in=$_GET['check_in']??'';$out=$_GET['check_out']??'';$guests=max(1,(int)($_GET['guests']??1));$type=(int)($_GET['type']??0);$valid=$in&&$out&&$out>$in&&$in>=date('Y-m-d');if(user()&&($in!==''||$out!==''||$type>0||$guests!==1)){db()->prepare('INSERT INTO user_search_history(user_id,check_in,check_out,guests,room_type_id) VALUES(?,?,?,?,?)')->execute([user()['id'],$in?:null,$out?:null,$guests,$type?:null]);}$where=["r.status='AVAILABLE'",'r.max_guests>=?'];$args=[$guests];if($type){$where[]='r.room_type_id=?';$args[]=$type;}if($valid){$where[]="NOT EXISTS(SELECT 1 FROM reservations x WHERE x.room_id=r.id AND ".reservation_conflict_sql().")";$args[]=$out;$args[]=$in;}$s=db()->prepare('SELECT r.*,t.name type_name FROM rooms r JOIN room_types t ON t.id=r.room_type_id WHERE '.implode(' AND ',$where).' ORDER BY r.price_per_night');$s->execute($args);$rooms=$s->fetchAll();$types=db()->query('SELECT id,name FROM room_types')->fetchAll();$pageTitle='Rooms & suites';require __DIR__.'/../includes/header.php';?><section class="page-hero"><div><p class="kicker">STAY WITH US</p><h1>Rooms & suites</h1><p>Designed for rest. Created for unforgettable stays.</p></div></section><section class="luxury-grid"><form class="booking-widget" method="get"><div><label>Check-in</label><input required type="date" min="<?=date('Y-m-d')?>" name="check_in" value="<?=e($in)?>"></div><div><label>Check-out</label><input required type="date" name="check_out" value="<?=e($out)?>"></div><div><label>Guests</label><input type="number" min="1" name="guests" value="<?=$guests?>"></div><div><label>Room type</label><select name="type"><option value="">All room types</option><?php foreach($types as $t):?><option value="<?=$t['id']?>" <?=$type===$t['id']?'selected':''?>><?=e($t['name'])?></option><?php endforeach;?></select></div><button>Find your room</button></form><div class="room-showcase"><?php foreach($rooms as $r):?><article class="luxury-room"><div class="room-photo" role="img" aria-label="<?=e($r['title'])?>">Room <?=e($r['room_number'])?></div><p class="kicker"><?=e($r['type_name'])?></p><h3><?=e($r['title'])?></h3><p class="muted"><?=e($r['description'])?></p><p class="muted"><?=e($r['size'])?> · <?=$r['max_guests']?> guests · <?=e($r['bed_type'])?></p><p class="price">From ₱<?=number_format((float)$r['price_per_night'])?> <small>per night</small></p><a class="btn small" href="details.php?id=<?=$r['id']?>&check_in=<?=e($in)?>&check_out=<?=e($out)?>&guests=<?=$guests?>">View room</a></article><?php endforeach;?></div><?php if(!$rooms):?><div class="panel"><p class="eyebrow-title">NO ROOMS AVAILABLE</p><h2>Try different dates or adjust your stay.</h2><a class="btn" href="index.php">Change dates</a></div><?php endif;?></section><?php require __DIR__.'/../includes/footer.php'; ?>
<?php
require __DIR__.'/../includes/bootstrap.php';
$in=$_GET['check_in']??''; $out=$_GET['check_out']??''; $guests=max(1,min(8,(int)($_GET['guests']??2))); $type=max(0,(int)($_GET['type']??0));
$destination=trim($_GET['destination']??''); $maxPrice=max(0,(int)($_GET['max_price']??0)); $sort=$_GET['sort']??'recommended';
$selectedAmenities=array_values(array_filter($_GET['amenities']??[],static fn($id)=>ctype_digit((string)$id)));
$validDates=$in!==''&&$out!==''&&$out>$in&&$in>=date('Y-m-d');
$types=db()->query('SELECT id,name FROM room_types ORDER BY name')->fetchAll(); $amenities=db()->query('SELECT id,name FROM amenities ORDER BY name')->fetchAll();
$where=["r.status='AVAILABLE'",'r.max_guests>=?']; $args=[$guests];
if($type){$where[]='r.room_type_id=?';$args[]=$type;} if($destination!==''){$where[]='(r.title LIKE ? OR t.name LIKE ? OR r.description LIKE ?)';$like='%'.$destination.'%';array_push($args,$like,$like,$like);} if($maxPrice){$where[]='r.price_per_night<=?';$args[]=$maxPrice;}
if($validDates){$where[]='NOT EXISTS(SELECT 1 FROM reservations x WHERE x.room_id=r.id AND '.reservation_conflict_sql().')';array_push($args,$out,$in);} foreach($selectedAmenities as $amenityId){$where[]='EXISTS(SELECT 1 FROM room_amenities filter_ra WHERE filter_ra.room_id=r.id AND filter_ra.amenity_id=?)';$args[]=(int)$amenityId;}
$ordering=['price_low'=>'r.price_per_night ASC','price_high'=>'r.price_per_night DESC','rating'=>'r.featured DESC,r.price_per_night ASC','recommended'=>'r.featured DESC,r.price_per_night ASC']; $order=$ordering[$sort]??$ordering['recommended'];
$sql='SELECT r.*,t.name type_name,(SELECT image_path FROM room_images ri WHERE ri.room_id=r.id ORDER BY ri.is_primary DESC,ri.id LIMIT 1) image_path,GROUP_CONCAT(DISTINCT a.name ORDER BY a.name SEPARATOR "|") amenity_names FROM rooms r JOIN room_types t ON t.id=r.room_type_id LEFT JOIN room_amenities ra ON ra.room_id=r.id LEFT JOIN amenities a ON a.id=ra.amenity_id WHERE '.implode(' AND ',$where).' GROUP BY r.id ORDER BY '.$order;
$statement=db()->prepare($sql);$statement->execute($args);$rooms=$statement->fetchAll(); $pageTitle='Find your stay'; require __DIR__.'/../includes/header.php';
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
    'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=900&q=80',
    'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=900&q=80'
];

$pageTitle = 'Available Rooms & Suites';
require __DIR__ . '/../includes/header.php';
?>

<section class="marketplace-page">
  <form class="search-panel" method="get" action="<?=url('rooms/index.php')?>">
    <div class="search-field destination"><label for="destination">Destination</label><input id="destination" name="destination" value="<?=e($destination)?>" placeholder="City, hotel, or room type"></div>
    <div class="search-field"><label for="check_in">Check-in</label><input id="check_in" type="date" min="<?=date('Y-m-d')?>" name="check_in" value="<?=e($in)?>"></div>
    <div class="search-field"><label for="check_out">Check-out</label><input id="check_out" type="date" min="<?=date('Y-m-d',strtotime('+1 day'))?>" name="check_out" value="<?=e($out)?>"></div>
    <div class="search-field"><label for="guests">Guests</label><select id="guests" name="guests"><?php for($n=1;$n<=8;$n++):?><option value="<?=$n?>" <?=$guests===$n?'selected':''?>><?=$n?> guest<?=$n>1?'s':''?></option><?php endfor;?></select></div><button class="search-button">Search rooms</button>
  </form>
  <?php if(($in||$out)&&!$validDates):?><div class="search-note">Choose a check-out date after check-in to view live availability.</div><?php endif;?>
  <section class="offer-panel" id="offers"><div><p class="offer-title">More value, right from the start</p><p>Flexible stays and thoughtful extras when you book directly with Jill Hotel.</p><div class="offer-chips"><span>Up to 10% off selected stays</span><span>Free cancellation options</span><span>Breakfast packages</span></div></div><a class="btn offer-button" href="#results">View rooms</a></section>
  <div class="results-layout" id="results"><button class="filter-toggle" type="button" data-filter-toggle aria-expanded="false">Filters</button><aside class="filter-sidebar" data-filter-panel><div class="filter-heading"><h2>Filters</h2><a href="<?=url('rooms/index.php')?>">Clear all</a></div><form method="get"><input type="hidden" name="destination" value="<?=e($destination)?>"><input type="hidden" name="check_in" value="<?=e($in)?>"><input type="hidden" name="check_out" value="<?=e($out)?>"><input type="hidden" name="guests" value="<?=$guests?>"><input type="hidden" name="sort" value="<?=e($sort)?>">
  <section class="filter-group"><h3>Budget per night</h3><label class="range-label" for="max_price">Up to <strong>₱<?=number_format($maxPrice?:10000)?></strong></label><input id="max_price" type="range" name="max_price" min="1000" max="10000" step="500" value="<?=$maxPrice?:10000?>" data-price-range><output data-price-output>₱<?=number_format($maxPrice?:10000)?></output></section>
  <section class="filter-group"><h3>Room type</h3><label class="check-row"><input type="radio" name="type" value="0" <?=$type===0?'checked':''?>> All rooms</label><?php foreach($types as $item):?><label class="check-row"><input type="radio" name="type" value="<?=$item['id']?>" <?=$type===$item['id']?'checked':''?>> <?=e($item['name'])?></label><?php endforeach;?></section>
  <section class="filter-group"><h3>Popular amenities</h3><?php foreach($amenities as $item):?><label class="check-row"><input type="checkbox" name="amenities[]" value="<?=$item['id']?>" <?=in_array((string)$item['id'],$selectedAmenities,true)?'checked':''?>> <?=e($item['name'])?></label><?php endforeach;?></section><button class="apply-filters">Apply filters</button></form></aside>
  <section class="results-column"><div class="results-header"><div><p class="results-count"><?=count($rooms)?> <?=count($rooms)===1?'room':'rooms'?> found</p><p class="results-subtitle">Available stays matched to your search</p></div><form method="get" class="sort-form"><?php foreach($_GET as $key=>$value):if($key!=='sort'&&$key!=='amenities'&&!is_array($value)):?><input type="hidden" name="<?=e($key)?>" value="<?=e((string)$value)?>"><?php endif;endforeach;foreach($selectedAmenities as $value):?><input type="hidden" name="amenities[]" value="<?=$value?>"><?php endforeach;?><label for="sort">Sort</label><select id="sort" name="sort" onchange="this.form.submit()"><option value="recommended" <?=$sort==='recommended'?'selected':''?>>Recommended</option><option value="price_low" <?=$sort==='price_low'?'selected':''?>>Price: low to high</option><option value="price_high" <?=$sort==='price_high'?'selected':''?>>Price: high to low</option><option value="rating" <?=$sort==='rating'?'selected':''?>>Featured first</option></select></form></div><div class="hotel-results">
  <?php foreach($rooms as $room):$roomAmenities=array_filter(explode('|',(string)$room['amenity_names']));$image=$room['image_path']?:'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=900&q=80';?><article class="hotel-result-card"><div class="hotel-image-wrap"><img src="<?=e($image)?>" alt="<?=e($room['title'])?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&amp;fit=crop&amp;w=900&amp;q=80';"><button class="favorite-button" type="button" data-favorite="<?=$room['id']?>" aria-label="Save <?=e($room['title'])?>">♡</button></div><div class="hotel-content"><p class="room-type"><?=e($room['type_name'])?></p><h2><?=e($room['title'])?></h2><div class="rating-line"><span class="rating-badge"><?=$room['featured']?'9.2':'8.7'?></span><strong><?=$room['featured']?'Excellent':'Very good'?></strong><span><?=$room['featured']?'48':'31'?> guest reviews</span></div><p class="location-line">Jill Hotel Manila · Convenient city location</p><p class="room-description"><?=e($room['description'])?></p><p class="room-meta">Up to <?=$room['max_guests']?> guests · <?=e($room['bed_type'])?> · <?=e($room['size'])?></p><div class="amenity-tags"><?php foreach(array_slice($roomAmenities,0,4) as $amenity):?><span><?=e($amenity)?></span><?php endforeach;?><span>Free cancellation</span></div></div><div class="hotel-price"><p>From</p><strong>₱<?=number_format((float)$room['price_per_night'])?></strong><span>per night</span><?php if($validDates):?><small>Total excludes taxes</small><?php endif;?><a class="btn availability-button" href="details.php?id=<?=$room['id']?>&amp;check_in=<?=e($in)?>&amp;check_out=<?=e($out)?>&amp;guests=<?=$guests?>">Check availability</a></div></article><?php endforeach;?></div><?php if(!$rooms):?><div class="empty-results"><h2>No rooms found</h2><p>Try changing your dates or removing a few filters.</p><a class="btn" href="<?=url('rooms/index.php')?>">Clear filters</a></div><?php endif;?></section></div>
</section><?php require __DIR__.'/../includes/footer.php';exit; ?>
    <form class="search-panel" method="get" action="<?=url('rooms/index.php')?>" aria-label="Room search parameters">
        <div class="search-field destination">
            <label for="destination">Destination or Keyword</label>
            <input id="destination" name="destination" value="<?=e($destination)?>" placeholder="City, room type, or feature">
        </div>
        <div class="search-field">
            <label for="check_in">Check-in</label>
            <input id="check_in" type="date" min="<?=date('Y-m-d')?>" name="check_in" value="<?=e($in)?>">
        </div>
        <div class="search-field">
            <label for="check_out">Check-out</label>
            <input id="check_out" type="date" min="<?=date('Y-m-d', strtotime('+1 day'))?>" name="check_out" value="<?=e($out)?>">
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
                            <strong>₱<?=number_format((float)$room['price_per_night'])?></strong>
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

<?php require __DIR__ . '/../includes/footer.php'; ?>
