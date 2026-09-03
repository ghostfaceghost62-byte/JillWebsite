</main>
<?php if ($isAdminShell ?? false): ?>
<script src="<?=url('assets/js/main.js?v=20260904v12')?>"></script>
</body>
</html>
<?php return; endif; ?>
<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            <h4>JILL HOTEL</h4>
            <p>A tranquil boutique sanctuary inspired by the natural beauty and warm hospitality of the Philippine islands. Every stay is crafted for deep rest and memorable moments.</p>
        </div>
        <div class="footer-col">
            <h5>Explore</h5>
            <ul>
                <li><a href="<?=url('rooms/index.php')?>">Rooms &amp; Suites</a></li>
                <li><a href="<?=url('amenities.php')?>">Amenities &amp; Wellness</a></li>
                <li><a href="<?=url('about.php')?>">Our Heritage</a></li>
                <li><a href="<?=url('contact.php')?>">Contact Concierge</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h5>Guest Services</h5>
            <ul>
                <?php if (user()): ?>
                    <li><a href="<?=url('customer/dashboard.php')?>">Guest Dashboard</a></li>
                    <li><a href="<?=url('customer/reservations.php')?>">My Stays</a></li>
                    <li><a href="<?=url('customer/profile.php')?>">Profile &amp; Preferences</a></li>
                <?php else: ?>
                    <li><a href="<?=url('auth/login.php')?>">Guest Sign In</a></li>
                    <li><a href="<?=url('auth/register.php')?>">Create an Account</a></li>
                    <li><a href="<?=url('auth/verify.php')?>">Verify Account</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="footer-col">
            <h5>Concierge &amp; Location</h5>
            <p style="color: rgba(250,248,245,0.75); font-size: 0.85rem; line-height: 1.6; margin-bottom: 0.75rem;">
                101 Seaside Avenue, Manila, Philippines<br>
                Direct: +63 2 8123 4567<br>
                Email: stay@hotelreserve.local
            </p>
            <p style="color: var(--accent-light, #DFC58C); font-size: 0.78rem; font-weight: 600;">Front Desk available 24 hours daily</p>
        </div>
    </div>
    <div class="footer-bottom">
        <div>&copy; <?=date('Y')?> Jill Hotel Reservation System. All rights reserved.</div>
        <div>All rates and charges are quoted in Philippine Pesos (₱)</div>
    </div>
</footer>
<script src="<?=url('assets/js/main.js?v=20260904v12')?>"></script>
<script>
(function() {
    let rates = null;
    const symbols = { 'PHP':'₱', 'USD':'$', 'EUR':'€', 'JPY':'¥', 'SGD':'S$', 'AUD':'A$', 'GBP':'£' };

    function fetchRates(callback) {
        if (rates) return callback();
        fetch('<?=url('api/currency.php')?>').then(r=>r.json()).then(d=>{ rates = d.rates; callback(); }).catch(console.error);
    }

    function formatAmount(amt, curr) {
        const sym = symbols[curr] || curr+' ';
        return sym + amt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function updatePrices() {
        const curr = localStorage.getItem('hotelreserve-currency') || 'PHP';
        if (curr === 'PHP') {
            document.querySelectorAll('[data-php-price]').forEach(el => {
                el.textContent = formatAmount(parseFloat(el.dataset.phpPrice), 'PHP');
            });
            return;
        }
        fetchRates(() => {
            const rate = rates[curr] || 1;
            document.querySelectorAll('[data-php-price]').forEach(el => {
                const base = parseFloat(el.dataset.phpPrice);
                el.textContent = formatAmount(base * rate, curr);
            });
        });
    }

    window.addEventListener('currencyChanged', updatePrices);
    
    // Initial run
    if (localStorage.getItem('hotelreserve-currency') && localStorage.getItem('hotelreserve-currency') !== 'PHP') {
        updatePrices();
    }
})();
</script>
</body>
</html>
