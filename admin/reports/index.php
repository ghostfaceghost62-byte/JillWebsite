<?php require __DIR__.'/../../includes/admin_auth.php';$pdo=db();$popular=$pdo->query("SELECT rm.title,COUNT(r.id) count FROM rooms rm LEFT JOIN reservations r ON r.room_id=rm.id GROUP BY rm.id ORDER BY count DESC,rm.title LIMIT 10")->fetchAll();$types=$pdo->query("SELECT t.name,COUNT(r.id) count FROM room_types t LEFT JOIN rooms rm ON rm.room_type_id=t.id LEFT JOIN reservations r ON r.room_id=rm.id GROUP BY t.id ORDER BY count DESC")->fetchAll();$pageTitle='Reports';require __DIR__.'/../../includes/header.php';?><h1>Hotel reports</h1><div class="grid"><div class="panel"><h2>Most reserved rooms</h2><?php foreach($popular as $r):?><p><?=e($r['title'])?> <b><?=e((string)$r['count'])?></b></p><?php endforeach;?></div><div class="panel"><h2>Popular room types</h2><?php foreach($types as $r):?><p><?=e($r['name'])?> <b><?=e((string)$r['count'])?></b></p><?php endforeach;?></div></div><?php require __DIR__.'/../../includes/footer.php'; ?>
<?php
require __DIR__ . '/../../includes/admin_auth.php';

$pdo = db();
$popular = $pdo->query("
    SELECT rm.title, rm.room_number, COUNT(r.id) AS count
    FROM rooms rm
    LEFT JOIN reservations r ON r.room_id = rm.id
    GROUP BY rm.id
    ORDER BY count DESC, rm.title
    LIMIT 10
")->fetchAll();

$types = $pdo->query("
    SELECT t.name, COUNT(r.id) AS count
    FROM room_types t
    LEFT JOIN rooms rm ON rm.room_type_id = t.id
    LEFT JOIN reservations r ON r.room_id = rm.id
    GROUP BY t.id
    ORDER BY count DESC
")->fetchAll();

$maxRoomCount = !empty($popular) ? max(array_column($popular, 'count')) : 1;
$maxRoomCount = $maxRoomCount > 0 ? $maxRoomCount : 1;

$maxTypeCount = !empty($types) ? max(array_column($types, 'count')) : 1;
$maxTypeCount = $maxTypeCount > 0 ? $maxTypeCount : 1;

$pageTitle = 'Performance & Analytics Reports';
require __DIR__ . '/../../includes/header.php';
?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">BUSINESS INTELLIGENCE</span>
        <h1 style="margin-bottom: 0.35rem;">Hotel Analytics & Reports</h1>
        <p style="color: var(--text-secondary, #5C625D);">Demand breakdown across suites, room categories, and booking volume.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <section class="panel">
        <span class="kicker" style="font-size: 0.7rem; margin-bottom: 0.2rem;">TOP PERFORMING SUITES</span>
        <h2 style="font-size: 1.35rem; margin-bottom: 1.5rem;">Most Reserved Rooms</h2>

        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <?php foreach ($popular as $r):
                $pct = round(($r['count'] / $maxRoomCount) * 100);
            ?>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem; font-size: 0.9rem;">
                        <strong><?=e($r['title'])?> <small class="muted">#<?=e($r['room_number'])?></small></strong>
                        <span><strong><?=e((string)$r['count'])?></strong> bookings</span>
                    </div>
                    <div style="height: 6px; background: var(--surface-muted, #F4EFE6); border-radius: 3px; overflow: hidden;">
                        <div style="width: <?=$pct?>%; height: 100%; background: var(--accent, #B89650); border-radius: 3px;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <span class="kicker" style="font-size: 0.7rem; margin-bottom: 0.2rem;">MARKET SEGMENTS</span>
        <h2 style="font-size: 1.35rem; margin-bottom: 1.5rem;">Popular Room Categories</h2>

        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <?php foreach ($types as $r):
                $pct = round(($r['count'] / $maxTypeCount) * 100);
            ?>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.35rem; font-size: 0.9rem;">
                        <strong><?=e($r['name'])?></strong>
                        <span><strong><?=e((string)$r['count'])?></strong> reservations</span>
                    </div>
                    <div style="height: 6px; background: var(--surface-muted, #F4EFE6); border-radius: 3px; overflow: hidden;">
                        <div style="width: <?=$pct?>%; height: 100%; background: var(--brand, #1C3328); border-radius: 3px;"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
