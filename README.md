# HOTELRESERVE — Jill Hotel Reservation System

HOTELRESERVE is a PHP and MySQL hotel reservation and hotel-management system for XAMPP. Guests can browse rooms and manage reservations, while hotel staff use a protected administration dashboard to manage rooms, reservations, reports, and activity logs.

## Technology

- PHP 8+ with PDO
- MySQL and Apache through XAMPP
- HTML, CSS, and vanilla JavaScript

## Run locally with XAMPP

1. Copy the project to `C:\xampp\htdocs\hotelreservation`.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open phpMyAdmin and import `database/hotelreservation_db.sql` into MySQL.
4. Copy `.env.example` to `.env` and update the database credentials for your local environment.
5. Visit `http://localhost/hotelreservation/`.

The application reads its database and app-root settings from environment variables first, then falls back to the XAMPP defaults in `config/database.php` for compatibility.

The application derives its URL path from the Apache-served project folder. `APP_ROOT` in `config/database.php` is only a fallback for unusual Apache aliases.

### Existing installation upgrade

Do not re-import `hotelreservation_db.sql` into an existing installation: it recreates the database tables and removes existing data. Instead, import `database/upgrade_auth_schema.sql` once in phpMyAdmin. It adds the current authentication fields and tables without deleting rooms, reservations, or users.

## Development admin account

After importing the main schema, use:

- Email: `admin@hotelreserve.local`
- Password: `Admin@12345`

The administrator is redirected to `admin/index.php` after a successful sign-in. Change development credentials before deploying.

## Authentication and access control

- New guest accounts must be verified with a 15-character verification code before sign-in.
- Passwords use PHP password hashing and verification APIs.
- Login attempts are throttled by IP address; a successful login clears the relevant failed attempts.
- Sessions regenerate on login and expire after 30 minutes of inactivity.
- Guest pages require an authenticated session; admin pages additionally require the `ADMIN` role.
- Signed-in administrators see **Admin Dashboard**, **Manage Rooms**, **Reservations**, and **Reports** in the navigation.

## Reservation workflow

1. Search by room keyword, dates, guests, type, budget, or amenities.
2. Review room details and availability.
3. Sign in or create and verify a guest account.
4. Submit a reservation request.
5. The server rechecks availability in a transaction before creating the reservation and pending payment record.

Overlapping active reservations for the same room are rejected; adjacent stays are allowed.

## Features

### Guest

- Room search, sorting, filters, availability, and room details
- Reservation creation and cancellation
- Profile, notification, and reservation-history pages
- Browser-local room favourites
- Responsive navigation and dark/light theme preference

### Administration

- Dashboard metrics for rooms, reservations, payments, and revenue
- Room-status management
- Reservation status updates with guest notifications
- Reports and activity logs

## Important folders

- `config/` — database configuration
- `includes/` — bootstrap, authentication middleware, and shared layout
- `auth/` — registration, verification, sign-in, sign-out, and password recovery
- `rooms/`, `reservations/`, `customer/` — guest workflows
- `admin/` — protected hotel-management pages
- `database/` — database schema, seed data, and non-destructive upgrade script
- `assets/` — CSS and JavaScript assets

## Verify the installation

1. Open `http://localhost/hotelreservation/auth/login.php`.
2. Sign in with the development admin account above.
3. Confirm you reach the Hotel Management Dashboard and can see the admin navigation links.
4. Register a guest account, use the displayed development verification code, then sign in.
5. Confirm a guest cannot open an `admin/` page and receives the 403 page.

## Troubleshooting

**Login shows an error or returns to the sign-in page**

- Confirm MySQL is running and `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS` in `config/database.php` are correct.
- For an older database, import `database/upgrade_auth_schema.sql`.
- Check that the admin user has `role = ADMIN`, `status = ACTIVE`, and `email_verified = 1`.

**Admin page does not appear after sign-in**

- Sign out and sign in again with an account whose `role` is `ADMIN`.
- Use the hotel database (`hotelreservation_db`), not the legacy `bookreserve_db` export.

**Styles or links are broken**

- Confirm Apache serves the project from the expected folder under `htdocs`.
- Open the site through Apache (`http://localhost/...`), not by opening PHP files directly from the filesystem.
"# JillWebsite" 


to run ngrok run this in cmd 

ngrok http 80


link for the ngrok 

https://contact-bacterium-bacteria.ngrok-free.dev/hotelreservation/


the link for the local 
 `http://localhost/hotelreservation/


admin credentials
gmail: admin@hotelreserve.local
pass: Admin@12345

this is the new one