# Changelog

All notable changes to this project will be documented here.

---

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
