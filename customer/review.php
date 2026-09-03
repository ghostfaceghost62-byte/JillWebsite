<?php
require __DIR__ . '/../includes/bootstrap.php';
require_login();

$pdo = db();
$reservationId = (int)($_GET['reservation_id'] ?? $_POST['reservation_id'] ?? 0);

// Fetch the reservation
$s = $pdo->prepare('
    SELECT r.*, rm.title, rm.room_number 
    FROM reservations r 
    JOIN rooms rm ON rm.id = r.room_id 
    WHERE r.id = ? AND r.user_id = ?
');
$s->execute([$reservationId, user()['id']]);
$reservation = $s->fetch();

if (!$reservation) {
    flash('error', 'Reservation not found.');
    header('Location: ' . url('customer/reservations.php'));
    exit;
}

if ($reservation['status'] !== 'CHECKED_OUT') {
    flash('error', 'You can only review a room after checkout.');
    header('Location: ' . url('customer/reservations.php'));
    exit;
}

// Check if already reviewed
$revStmt = $pdo->prepare('SELECT id FROM reviews WHERE reservation_id = ?');
$revStmt->execute([$reservationId]);
if ($revStmt->fetchColumn()) {
    flash('info', 'You have already reviewed this stay.');
    header('Location: ' . url('customer/reservations.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    
    $rating = (int)($_POST['rating'] ?? 5);
    if ($rating < 1 || $rating > 5) $rating = 5;
    
    $title = trim($_POST['title'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    
    if ($title === '' || $comment === '') {
        flash('error', 'Please provide a title and your feedback.');
    } else {
        try {
            $pdo->prepare('
                INSERT INTO reviews (user_id, room_id, reservation_id, rating, title, comment)
                VALUES (?, ?, ?, ?, ?, ?)
            ')->execute([
                user()['id'],
                $reservation['room_id'],
                $reservationId,
                $rating,
                $title,
                $comment
            ]);
            
            log_action(user()['id'], 'SUBMIT_REVIEW', 'room', $reservation['room_id'], "Submitted {$rating}-star review for room {$reservation['room_number']}");
            
            flash('success', 'Thank you for your review!');
            header('Location: ' . url('customer/reservations.php'));
            exit;
        } catch (PDOException $e) {
            flash('error', 'Could not submit your review. Please try again later.');
        }
    }
}

$pageTitle = 'Leave a Review';
require __DIR__ . '/../includes/header.php';
?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">GUEST FEEDBACK</span>
        <h1 style="margin-bottom: 0.35rem;">Leave a Review</h1>
        <p style="color: var(--text-secondary, #5C625D);">Tell us about your stay in <?=e($reservation['title'])?>.</p>
    </div>
    <a class="btn small btn-outline" href="<?=url('customer/reservations.php')?>">Back to My Stays</a>
</div>

<div class="panel" style="max-width: 600px; margin: 0 auto;">
    <form method="post">
        <input type="hidden" name="csrf" value="<?=csrf()?>">
        <input type="hidden" name="reservation_id" value="<?=$reservationId?>">
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display:block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.85rem;">Rating</label>
            <div class="rating-select" style="display: flex; gap: 0.5rem;">
                <?php for($i=1; $i<=5; $i++): ?>
                    <label style="cursor: pointer;">
                        <input type="radio" name="rating" value="<?=$i?>" <?=$i==5?'checked':''?> style="display:none;">
                        <span class="star-icon" style="font-size: 2rem; color: #ccc;" data-val="<?=$i?>">★</span>
                    </label>
                <?php endfor; ?>
            </div>
            <style>
                .rating-select input:checked ~ .star-icon,
                .rating-select label:hover .star-icon,
                .rating-select label:hover ~ label .star-icon { color: #f1c40f !important; }
                .rating-select { direction: rtl; justify-content: flex-end; }
                .rating-select label:hover ~ label .star-icon { color: #f1c40f !important; }
            </style>
            <script>
                // Make the CSS sibling selector work for LTR by reversing logic, or handle via JS
                document.querySelectorAll('.star-icon').forEach(star => {
                    star.addEventListener('click', (e) => {
                        let val = parseInt(e.target.dataset.val);
                        document.querySelectorAll('.star-icon').forEach(s => {
                            s.style.color = parseInt(s.dataset.val) <= val ? '#f1c40f' : '#ccc';
                        });
                    });
                });
                // Initialize default
                document.querySelectorAll('.star-icon').forEach(s => s.style.color = '#f1c40f');
            </script>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display:block; font-weight: 600; margin-bottom: 0.25rem; font-size: 0.85rem;" for="title">Summary of your experience <span style="color: #c0392b;">*</span></label>
            <input type="text" id="title" name="title" required maxlength="160" placeholder="e.g. Excellent stay, highly recommended!" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px;">
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display:block; font-weight: 600; margin-bottom: 0.25rem; font-size: 0.85rem;" for="comment">Detailed Feedback <span style="color: #c0392b;">*</span></label>
            <textarea id="comment" name="comment" required rows="5" placeholder="What did you love about your stay?" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 4px; resize: vertical;"></textarea>
        </div>

        <button type="submit" class="btn btn-gold" style="width: 100%;">Submit Review</button>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
