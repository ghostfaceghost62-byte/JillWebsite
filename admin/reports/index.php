<?php
$pageTitle = 'Reports & Exports';
require __DIR__ . '/../../includes/header.php';
require_admin();
?>

<div class="admin-layout">
    <?php require __DIR__ . '/../../includes/admin_sidebar.php'; ?>
    <main class="admin-content">
        <header class="admin-header">
            <h1>Reports & Exports</h1>
        </header>

        <section class="admin-panel" style="padding: 1.5rem; max-width: 600px; margin-top: 1rem;">
            <h2>Export Reservations</h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Generate financial and occupancy reports for past or upcoming reservations.</p>
            
            <form action="export_pdf.php" method="get" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <label for="pdf_start">Start Date</label>
                    <input type="date" id="pdf_start" name="start" required class="form-control" style="width:100%; padding: 0.5rem;" value="<?=date('Y-m-01')?>">
                </div>
                <div style="flex: 1;">
                    <label for="pdf_end">End Date</label>
                    <input type="date" id="pdf_end" name="end" required class="form-control" style="width:100%; padding: 0.5rem;" value="<?=date('Y-m-t')?>">
                </div>
                <div style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn">Generate PDF</button>
                </div>
            </form>
            
            <hr style="border: none; border-top: 1px solid var(--border); margin: 1.5rem 0;">

            <form action="export_excel.php" method="get" style="display: flex; gap: 1rem;">
                <div style="flex: 1;">
                    <label for="xls_start">Start Date</label>
                    <input type="date" id="xls_start" name="start" required class="form-control" style="width:100%; padding: 0.5rem;" value="<?=date('Y-m-01')?>">
                </div>
                <div style="flex: 1;">
                    <label for="xls_end">End Date</label>
                    <input type="date" id="xls_end" name="end" required class="form-control" style="width:100%; padding: 0.5rem;" value="<?=date('Y-m-t')?>">
                </div>
                <div style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn" style="background: #27ae60; color: white; border: none;">Generate Excel</button>
                </div>
            </form>
        </section>
    </main>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
