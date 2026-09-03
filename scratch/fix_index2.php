<?php
$file = 'd:/User/Downloads/Code VS/Jill Hotel Reservation Website (with data base/rooms/index.php';
$lines = file($file);

// Find the start of the unminified HTML part. It looks like line 108: `    <form class="search-panel"`
$startIndex = 0;
foreach ($lines as $i => $line) {
    if (strpos($line, '    <form class="search-panel"') !== false) {
        $startIndex = $i;
        break;
    }
}

// We also need the PHP logic at the top of the unminified section (around line 14-99).
// I will extract the PHP block that was between line 14 and the end of PHP.
$phpLogic = [];
$inPhp = false;
foreach ($lines as $i => $line) {
    if ($i > 10 && strpos($line, 'require __DIR__ . \'/../includes/bootstrap.php\';') !== false) {
        $inPhp = true;
        $phpLogic[] = "<?php\n";
    }
    if ($inPhp) {
        $phpLogic[] = $line;
        if (strpos($line, 'require __DIR__ . \'/../includes/header.php\';') !== false) {
            break;
        }
    }
}

$htmlLogic = array_slice($lines, $startIndex);

$flatpickr = <<<HTML
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.date-picker-input { background: var(--surface); border: 1px solid var(--border); padding: 0.65rem; font-size: 0.95rem; width: 100%; border-radius: 4px; color: var(--text-primary); cursor: pointer; outline: none; }
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

$htmlStr = implode("", $htmlLogic);
$htmlStr = str_replace('<?php require __DIR__ . \'/../includes/footer.php\'; ?>', $flatpickr, $htmlStr);

// Replace currency prices
$htmlStr = str_replace(
    '<strong>₱<?=number_format((float)$room[\'price_per_night\'])?></strong>',
    '<strong><span data-php-price="<?=(float)$room[\'price_per_night\']?>">₱<?=number_format((float)$room[\'price_per_night\'])?></span></strong>',
    $htmlStr
);

$content = implode("", $phpLogic) . "\n<section class=\"marketplace-page\">\n" . $htmlStr;

file_put_contents($file, $content);
echo "Fully cleaned rooms/index.php\n";
