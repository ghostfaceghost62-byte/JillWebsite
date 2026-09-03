<?php
require __DIR__ . '/../../includes/admin_auth.php';

$rows = db()->query("
    SELECT l.*, CONCAT(COALESCE(u.first_name,'System'),' ',COALESCE(u.last_name,'')) AS name
    FROM activity_logs l
    LEFT JOIN users u ON u.id = l.user_id
    ORDER BY l.created_at DESC
    LIMIT 100
")->fetchAll();

$pageTitle = 'Activity &amp; Audit Logs';
require __DIR__ . '/../../includes/header.php';
?>

<div class="admin-page-heading">
    <div>
        <span class="kicker">SYSTEM AUDIT TRAIL</span>
        <h1>Activity &amp; Security Logs</h1>
        <p>Historical log of all administrative actions, sign-ins, and reservation events.</p>
    </div>
</div>

<div class="admin-panel">
    <div class="table-scroll">
        <table class="admin-tbl">
            <thead>
                <tr>
                    <th>User / Operator</th>
                    <th>Event Action</th>
                    <th>Description</th>
                    <th>Entity</th>
                    <th>IP Address</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><strong><?=e($r['name'])?></strong></td>
                        <td><span class="s-badge pending" style="font-size:0.7rem;"><?=e($r['action'])?></span></td>
                        <td><?=e($r['description'])?></td>
                        <td><small style="color:var(--text-secondary);font-size:0.78rem;"><?=e($r['entity_type'] . ' #' . $r['entity_id'])?></small></td>
                        <td><code style="font-size:0.78rem;"><?=e($r['ip_address'] ?? '—')?></code></td>
                        <td><small style="color:var(--text-secondary);"><?=date('M d, Y &middot; g:i A', strtotime($r['created_at']))?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
