I already have a working hotel reservation website with an existing PHP backend, MySQL database, authentication system, reservation logic, guest area, and admin interface.

I only want to redesign and improve the front end. Do not rebuild, replace, or modify the backend functionality.

Your task is to transform the existing website into a polished luxury hotel reservation interface while preserving all existing functionality, routes, database queries, form actions, sessions, authentication, permissions, and business logic.

Important constraints:

- Do not change the database schema.
- Do not change PHP backend logic unless absolutely necessary for displaying existing data.
- Do not remove or rename existing form fields, input names, IDs, action URLs, query parameters, session variables, or route links.
- Do not break login, registration, verification, password recovery, reservations, cancellations, favorites, notifications, profile settings, or admin features.
- Preserve all existing PHP variables, loops, conditionals, includes, and server-side validation.
- Keep the existing backend data flow exactly as it is.
- Only change the visual layer, markup structure where safe, CSS, responsive behavior, and front-end interactions.
- If a UI change requires a backend change, explain it first instead of making destructive changes.
- Use the existing dynamic room, amenity, user, reservation, and admin data.
- Do not replace dynamic PHP content with hardcoded demo data.

Design direction:

Create a luxury boutique hotel editorial design inspired by premium resorts in the Philippines.

Use:

- Warm ivory and cream backgrounds
- Deep forest green, charcoal, and black feature sections
- Muted gray body copy
- Soft gold accent color
- Elegant serif headings
- Clean modern sans-serif body text
- Large immersive hotel imagery
- Generous whitespace
- Refined borders and subtle shadows
- Philippine peso pricing using the ₱ symbol
- High-end, calm, editorial visual hierarchy

Redesign the following existing areas:

1. Shared layout
- Improve the shared PHP header, navigation, and footer visually.
- Keep all existing includes and PHP logic.
- Preserve the current user role behavior.
- The navigation should visually adapt for:
  - Visitors
  - Signed-in guests
  - Administrators
- Keep all existing links and destinations.
- Add a responsive mobile navigation presentation below 760px without breaking existing navigation behavior.

2. Public homepage
Create a luxury hotel homepage with:

- Large hero section
- Hotel background image with readable overlay
- Luxury headline and supporting copy
- Existing booking/search form styled as a floating booking panel
- Featured room cards
- Amenity preview
- Hotel experience/story section
- Dark feature band
- Editorial image gallery
- Call-to-action sections

Use the existing room and amenity data from PHP/MySQL.

3. Room search/results page
Improve the front end of the existing room search page with:

- Destination search field
- Check-in and check-out date fields
- Guest selector
- Price or budget slider
- Room type filters
- Amenity filters
- Sorting controls for:
  - Recommended
  - Price low to high
  - Price high to low
  - Featured
- Responsive filter layout
- Attractive room result cards
- Empty-state design when no rooms match
- Loading and submission states where the current functionality supports them

Do not change the existing search query logic or parameter names.

4. Room details page
Redesign the room details page with:

- Large room image gallery
- Room title and room type
- Price per night in Philippine pesos
- Guest capacity
- Bed type
- Amenities
- Description
- Availability information
- Existing reservation form styled as a premium booking card
- Clear reservation call-to-action
- Existing validation and submission behavior preserved

5. Amenities, About, and Contact pages
Apply the same luxury visual system to:

- Amenities page
- About page
- Contact page

Keep all existing content, forms, links, and backend behavior intact.

6. Authentication pages
Redesign these pages without changing their functionality:

- Registration
- Sign in
- Email verification
- Password recovery
- Password reset

Use clean centered cards, clear validation messages, accessible labels, and responsive layouts.

Preserve all existing field names, action URLs, CSRF fields, error messages, and session behavior.

7. Guest area
Improve the visual design of:

- Guest dashboard
- Bookings
- Booking history
- Profile
- Notifications
- Saved favorites
- Account settings

Use dashboard cards, status badges, clean tables, responsive layouts, and empty states.

Do not change how data is loaded or updated.

8. Admin interface
Redesign the existing admin interface with:

- Luxury dashboard styling
- Summary statistic cards
- Reservation tables
- Room management cards or tables
- Reports layout
- Activity log layout
- Clear status badges
- Responsive admin navigation
- Desktop-friendly data tables
- Mobile-friendly table behavior

Keep all admin permissions, forms, actions, filters, and backend logic unchanged.

9. Theme support
Improve the existing light/dark mode implementation.

- Preserve the existing localStorage theme behavior.
- Preserve server-side preference saving for signed-in users.
- Use CSS custom properties for colors.
- Ensure both themes have readable contrast.
- Add a polished theme toggle in the shared navigation.

10. Favorites
Preserve the existing favorite behavior:

- Logged-in users must continue using their account-based favorites.
- Guests must continue using browser local storage if that already exists.
- Redesign the favorite button and saved state visually.
- Do not replace the existing favorite endpoint or data logic.

11. Forms and feedback
Preserve all existing forms and their behavior.

Where supported by the current code:

- Disable the submit button after submission begins.
- Change the button label to “Processing…”.
- Display success and error feedback clearly.
- Keep server-side validation as the source of truth.
- Do not remove hidden fields, CSRF tokens, IDs, names, or action attributes.

12. Accessibility
Improve the front end using:

- Semantic HTML5 landmarks
- Proper heading hierarchy
- Labels for all form inputs
- ARIA attributes where appropriate
- Descriptive image alt text
- Visible focus styles
- Keyboard-accessible controls
- Sufficient color contrast
- Meaningful empty and error states
- Responsive layouts without horizontal overflow

File organization:

- Keep PHP templates in their existing locations.
- Keep shared header, navigation, and footer templates.
- Organize front-end styles under:
  - assets/css/base.css
  - assets/css/luxury.css
  - assets/css/responsive.css
  - assets/css/search-results.css
  - assets/css/theme.css
- Organize front-end scripts under the existing assets/js directory.
- Reuse existing assets where possible.
- Use fallback hotel imagery only when a room image is missing.
- Do not hardcode database records.

Before editing:

1. Inspect the existing project structure.
2. Identify the shared layout files.
3. Identify all public, guest, authentication, and admin pages.
4. Identify existing CSS and JavaScript files.
5. Identify all backend variables, form actions, routes, and database-driven loops.
6. Create a safe front-end redesign plan.
7. List any files that will be modified.
8. Confirm that backend behavior will remain unchanged.

Then implement the redesign incrementally.

The final result should look like a premium luxury hotel reservation website while continuing to use my existing PHP backend, MySQL data, authentication, reservations, guest features, favorites, and admin tools.