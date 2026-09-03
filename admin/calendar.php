<?php
require __DIR__ . '/../includes/admin_auth.php';

$pageTitle = 'Booking Calendar';
require __DIR__ . '/../includes/header.php';
?>

<div class="admin-content">
    <header class="admin-header">
        <h1>Booking Calendar</h1>
    </header>

    <section class="admin-panel" style="padding: 1.5rem;">
        <div id="calendar"></div>
    </section>
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
    overflow: hidden;
}
.fc-theme-standard td, .fc-theme-standard th {
    border-color: var(--border);
}
.fc-col-header-cell-cushion, .fc-daygrid-day-number {
    color: var(--text-primary);
}
/* Prevent event text from overflowing cells */
.fc-daygrid-event {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 0.75rem;
}
.fc-daygrid-event-harness {
    overflow: hidden;
}
.fc-daygrid-day-frame {
    overflow: hidden;
}
.fc-event-title {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.fc-h-event {
    overflow: hidden;
}
.fc-daygrid-block-event .fc-event-title {
    font-size: 0.72rem;
    padding: 0 2px;
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
            info.jsEvent.preventDefault();
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
