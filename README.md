# Popular AI Software Search

A **modern, AJAX-powered WordPress plugin** for advanced post searching, featuring Alpine.js reactivity, dynamic keyword autosuggest, sortable results, star ratings, and Elementor compatibility. Designed to scale for sites with tens of thousands of posts.

---

## 🚀 Features

- **Dynamic search** with keyword autosuggest (stopwords filtered)
- **Category filtering** with live AJAX updates (no page reload)
- **Alpine.js-powered UI** for fast, reactive frontend
- **Sortable, toggleable results** (Grid/List view, custom table sort)
- **User star ratings** (1-5 stars, per post, registered users)
- **Forum-style comments** (threaded, via native WP comments API)
- **Elementor Widget & Shortcode support** for flexible embedding
- **Fully customizable** via WordPress Admin (settings page)
- **Yoast SEO integration** (fetches SEO titles, meta, keywords)
- **Scalable** for 20k+ posts (efficient SQL, caching, REST API)
- **Namespaced JavaScript/CSS** for zero conflicts with Elementor or other builders

---

## 💡 Tech Stack & Philosophy

- **WordPress REST API**: Modern, decoupled backend for AJAX and dynamic interactions
- **Alpine.js**: Lightweight, declarative frontend JS for reactivity without heavy frameworks
- **PHP/MySQL**: Native WordPress development, compatible with shared hosting (SiteGround, etc.)
- **Elementor integration**: Registers as a widget and shortcode
- **Performance-focused**: Smart DB queries, selective enqueues, caching/transients

---

## 🛠️ Installation

1. Upload plugin folder to `wp-content/plugins/`.
2. Activate **Popular AI Software Search** in WP admin.
3. Add `[popular_ai_software_search]` shortcode to any post/page **or** use the Elementor widget.
4. (Coming soon) Configure settings under WP Admin → Popular AI Search.

---

## 🏗️ Roadmap

- [x] Scaffold plugin structure, enqueue Alpine.js, and register shortcode
- [ ] Build admin settings page for UI/config
- [ ] Implement REST API endpoints for search, autosuggest, and rating
- [ ] Build Alpine.js-powered search and results UI
- [ ] Integrate star rating and comments
- [ ] Add Elementor widget support
- [ ] Optimize for large datasets (indexing, caching)
- [ ] Final polish, docs, and testing

---

## 📁 Folder Structure
/popular-ai-software-search/
/admin/ # Settings, admin views (coming soon)
/assets/
/css/ # Styles
/js/ # JS modules (Alpine components, AJAX, etc.)
/includes/ # Plugin logic (shortcodes, REST endpoints)
popular-ai-software-search.php
readme.md
changelog.md


---

## 💬 Technical Highlights (for Employers)

- **Built with best practices**: WP REST API, custom endpoints, secure nonces/roles, minimal dependencies.
- **Component-based frontend**: Alpine.js for modularity, rapid development, and future-proofing.
- **Optimized for shared hosting**: No dependencies on Node.js, external services, or shell access—runs on standard LAMP stack.
- **Extendable**: Can be used as a base for advanced post directories, rating sites, or knowledge bases.

---

## 🤝 Credits

Developed by Ronald Allan Rivera.  
Portfolio: allanwebdesign.com

---

## 📝 License

MIT License (or GPL v2, if distributing in WordPress repo).
