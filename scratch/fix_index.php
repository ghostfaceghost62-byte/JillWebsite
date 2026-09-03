<?php
$file = 'd:/User/Downloads/Code VS/Jill Hotel Reservation Website (with data base/rooms/index.php';
$lines = file($file);

// Find where the real unminified file begins
$startIndex = 0;
foreach ($lines as $i => $line) {
    if (strpos($line, '$in = $_GET[\'check_in\'] ?? \'\';') !== false && strpos($line, 'max(') === false) {
        $startIndex = $i - 1; // back up to the require bootstrap
        break;
    }
}

// Slice to get only the real file
$realFile = array_slice($lines, $startIndex - 1); // include <?php

// Now apply flatpickr modifications
$content = implode("", $realFile);

// Replace inputs
$content = str_replace(
    '<input id="check_in" type="date" min="<?=date(\'Y-m-d\')?>" name="check_in" value="<?=e($in)?>">',
    '<input id="check_in" class="date-picker-input" type="text" name="check_in" value="<?=e($in)?>" placeholder="Select date" readonly>',
    $content
);
$content = str_replace(
    '<input id="check_out" type="date" min="<?=date(\'Y-m-d\', strtotime(\'+1 day\'))?>" name="check_out" value="<?=e($out)?>">',
    '<input id="check_out" class="date-picker-input" type="text" name="check_out" value="<?=e($out)?>" placeholder="Select date" readonly>',
    $content
);

// Append flatpickr logic before footer
$flatpickr = <<<HTML
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.date-picker-input { background: transparent; border: none; font-size: 1rem; width: 100%; color: var(--text-primary); cursor: pointer; outline: none; }
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
HTML;

$content = str_replace('<?php require __DIR__ . \'/../includes/footer.php\'; ?>', $flatpickr, $content);

file_put_contents($file, $content);
echo "Cleaned and updated rooms/index.php\n";
