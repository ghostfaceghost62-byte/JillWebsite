</main><footer><b>Jill Hotel</b> · 101 Seaside Avenue, Manila · +63 2 8123 4567 · stay@hotelreserve.local<br>© <?=date('Y')?> Find Your Stay. Reserve Your Room. Enjoy Your Experience.</footer><script src="<?=url('assets/js/main.js?v=2026090402')?>"></script></body></html>
</main>
<footer>
    <div class="footer-inner">
        <div class="footer-brand">
            <h4>JILL HOTEL</h4>
            <p>A tranquil boutique sanctuary inspired by the natural beauty and warm hospitality of the Philippine islands. Every stay is crafted for deep rest and memorable moments.</p>
        </div>
        <div class="footer-col">
            <h5>Explore</h5>
            <ul>
                <li><a href="<?=url('rooms/index.php')?>">Rooms & Suites</a></li>
                <li><a href="<?=url('amenities.php')?>">Amenities & Wellness</a></li>
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
                    <li><a href="<?=url('customer/profile.php')?>">Profile & Preferences</a></li>
                <?php else: ?>
                    <li><a href="<?=url('auth/login.php')?>">Guest Sign In</a></li>
                    <li><a href="<?=url('auth/register.php')?>">Create an Account</a></li>
                    <li><a href="<?=url('auth/verify.php')?>">Verify Account</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="footer-col">
            <h5>Concierge & Location</h5>
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
<script src="<?=url('assets/js/main.js?v=20260904v2')?>"></script>
</body>
</html>
