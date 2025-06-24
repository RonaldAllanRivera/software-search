# Changelog

All notable changes to this project will be documented here.

---

## [0.3.0] – 2025-06-24

### Added
- Sortable results table in list view: users can click Title, Category, or Date headers to sort results instantly via AJAX
- Ascending/descending toggle per column, with clear arrow icon in header
- Sort state persists across searches, filters, and pagination
- Whole-word search customization for keyword: ensures only true matches (no substring matches like "Excuses" for "sex")
- All features (autosuggest, categories, pagination, grid/list toggle) work seamlessly with sorting and new search logic

### Improved
- Polished the user experience for sorting, navigation, and search accuracy
- Modularized JS and PHP: easy to extend for future rating/comments features


## [0.2.0] – 2025-06-24

### Added
- Pagination controls (Prev/Next buttons) for AJAX-powered results, with real-time UI update and state
- Grid/List view toggle: switch between results grid and sortable table view (UI toggle, CSS highlighting)
- Dynamic results rendering for both formats, with seamless switching and pagination awareness
- Results pagination status display (current page and total pages)
- Polished user experience for searching, filtering, view switching, and paging through large datasets

### Improved
- Ensured dynamic categories and autosuggest remain fully functional with pagination and view switching
- Confirmed REST API URL detection is robust for any subfolder or local WP install
- All new features tested and working with Elementor, shared hosting, and large post counts

### Technical
- JS modularization: Results rendering, pagination, and view switching cleanly separated for maintainability
- Added CSS classes and structure for easy future style extension


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
