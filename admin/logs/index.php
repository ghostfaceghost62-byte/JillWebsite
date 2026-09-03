<?php require __DIR__.'/../../includes/admin_auth.php';$rows=db()->query("SELECT l.*,CONCAT(COALESCE(u.first_name,'System'),' ',COALESCE(u.last_name,'')) name FROM activity_logs l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.created_at DESC LIMIT 100")->fetchAll();$pageTitle='Activity logs';require __DIR__.'/../../includes/header.php';?><h1>Activity logs</h1><div class="panel"><table><thead><tr><th>User</th><th>Action</th><th>Description</th><th>Entity</th><th>IP</th><th>Date</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=e($r['name'])?></td><td><span class="badge"><?=e($r['action'])?></span></td><td><?=e($r['description'])?></td><td><?=e($r['entity_type'].' #'.$r['entity_id'])?></td><td><?=e($r['ip_address'])?></td><td><?=e($r['created_at'])?></td></tr><?php endforeach;?></tbody></table></div><?php require __DIR__.'/../../includes/footer.php'; ?>
<?php
require __DIR__ . '/../../includes/admin_auth.php';

$rows = db()->query("
    SELECT l.*, CONCAT(COALESCE(u.first_name,'System'),' ',COALESCE(u.last_name,'')) AS name
    FROM activity_logs l
    LEFT JOIN users u ON u.id = l.user_id
    ORDER BY l.created_at DESC
    LIMIT 100
")->fetchAll();

$pageTitle = 'Activity & Audit Logs';
require __DIR__ . '/../../includes/header.php';
?>

<div class="section-head" style="margin-bottom: 2rem;">
    <div>
        <span class="kicker">SYSTEM AUDIT TRAIL</span>
        <h1 style="margin-bottom: 0.35rem;">Activity & Security Logs</h1>
        <p style="color: var(--text-secondary, #5C625D);">Historical log of all administrative actions, sign-ins, and reservation events.</p>
    </div>
</div>

<div class="panel table-scroll">
    <table>
        <thead>
            <tr>
                <th>User / Operator</th>
                <th>Event Action</th>
                <th>Event Description</th>
                <th>Entity Reference</th>
                <th>IP Address</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><strong><?=e($r['name'])?></strong></td>
                    <td><span class="badge"><?=e($r['action'])?></span></td>
                    <td><?=e($r['description'])?></td>
                    <td><small style="color: var(--text-secondary, #7A807B);"><?=e($r['entity_type'] . ' #' . $r['entity_id'])?></small></td>
                    <td><code style="font-size: 0.78rem;"><?=e($r['ip_address'] ?? '127.0.0.1')?></code></td>
                    <td><small style="color: var(--text-secondary, #7A807B);"><?=date('M d, Y &middot; g:i A', strtotime($r['created_at']))?></small></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
