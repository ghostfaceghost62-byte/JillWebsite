<?php
$pageTitle = 'Booking Calendar';
require __DIR__ . '/../includes/header.php';
require_admin();
?>

<div class="admin-layout">
    <?php require __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <main class="admin-content">
        <header class="admin-header">
            <h1>Booking Calendar</h1>
        </header>

        <section class="admin-panel" style="padding: 1.5rem;">
            <div id="calendar"></div>
        </section>
    </main>
</div>

<!-- FullCalendar Core & Plugins -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
    background: var(--surface);
    border-radius: 8px;
    padding: 1rem;
}
.fc-theme-standard td, .fc-theme-standard th {
    border-color: var(--border);
}
.fc-col-header-cell-cushion, .fc-daygrid-day-number {
    color: var(--text-primary);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '<?=url('admin/api/bookings.php?type=events')?>',
        eventClick: function(info) {
            info.jsEvent.preventDefault(); // don't let the browser navigate
            if (info.event.url) {
                window.open(info.event.url, '_blank');
            }
        },
        height: 'auto',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        }
    });

    calendar.render();
});
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>

