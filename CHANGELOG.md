# Changelog

All notable changes to this project will be documented here.

---

## [0.1.3] - 2025-07-01

### Added
- Prominent, centered star rating widget on all single post pages with bold header, mouseover highlight, and instant voting.
- Ratings stored in custom table, fetched and rendered via AJAX for all posts.
- Results table now displays average star ratings in yellow, matching frontend design.
- Categories, Comments, Rating, Date, and Link columns are now single-line, never wrapping for a compact look.

### Improved
- All REST API calls now use a robust dynamic base path for full compatibility with subdirectory, multisite, or localhost installs.
- Visual polish for ratings table and single post UX.

### Technical
- Uses `pais_vars.rest_url` (localized via PHP) in all frontend JS for reliable API routing.

## [0.1.2] - 2025-06-25

### Fixed
- Pagination now works correctly when using whole-word keyword search filtering in PHP.
- REST API returns the real number of results and total pages after filtering.
- Pagination UI is now always accurate for all keyword and category search cases.

### Technical
- Refactored search endpoint to filter matching posts in PHP, then paginate the filtered results before returning to the frontend.


## [0.1.1] - 2025-06-24

### Fixed
- Removed all unsupported SQL REGEXP logic from custom REST search endpoint.
- Switched to native WordPress `'s'` argument for keyword search for full MySQL/MariaDB compatibility.
- Confirmed that keyword search now works on both local and production servers.

### Known Issues
- Pagination does not work correctly in search results (to be fixed in the next session).

## [0.1.0] - 2025-06-23

### Added
- Initial plugin scaffold, shortcode UI, and asset loading (PHP/JS/CSS)
- Custom REST API endpoint: /popularai/v1/search for live post search
- Custom REST API endpoint: /popularai/v1/autosuggest for keyword suggestions (stopwords filtered)
- Vanilla JS (ES6+) + Fetch for all AJAX UI (no jQuery or frameworks)
- Real-time autosuggest for keywords, driven by REST API
- Dynamic category dropdown, populated via WP REST /wp/v2/categories
- Responsive results rendering (title, excerpt, permalink, category, date)
- "No results found" message for empty queries
- Fully dynamic, no page reloads; compatible with all themes/builders

### Technical
- JS dynamically picks up correct REST API base URL for subfolders/multisite
- All assets only load on shortcode pages
- Security: output and REST inputs fully sanitized
