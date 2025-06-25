# Popular AI Software Search

A **modern, AJAX-powered WordPress plugin** for advanced post searching.
Built for scale, with fast keyword autosuggest, category filtering, sortable results, and robust admin controls.
**No jQuery, no frameworks—just Vanilla JavaScript (ES6+) and the Fetch API for maximum speed and compatibility.**

---

## 🚀 Features

* **Dynamic search** with live keyword autosuggest (stopwords filtered)
* **Category filtering** and real-time UI updates (no page reload)
* **Grid/List view toggle** for flexible result display
* **Sortable, paginated results** (title, category, star rating, comments, date) — Pagination and total count now always match filtered (keyword/category) search!
* **User star ratings** (1-5, per post, registered users)
* **Threaded comments** via native WP Comments API (forum-style)
* **Elementor Widget & Shortcode support**
* **Fully customizable** via WordPress Admin settings
* **Yoast SEO integration** (SEO titles, meta, keywords)
* **Scalable** for 20k+ posts (optimized SQL, REST API, caching)
* **Zero JS conflicts:** all JS and CSS are namespaced, loaded only when needed

---

### 🆕 What's New (v0.1.2)
- Accurate pagination with whole-word keyword search: Backend now filters and paginates after PHP-side keyword filtering for 100% correct total/pages.
- MySQL/MariaDB compatibility: All custom REGEXP logic removed. Uses native WordPress search and safe, universal PHP filtering.
- Search UI and REST API now always display the correct number of results and pages, no matter the keyword or category filter.
---


## 💡 Tech Stack & Philosophy

* **Frontend:**

  * **Vanilla JavaScript (ES6+)** for all UI interactivity
  * **Fetch API** for AJAX (REST endpoints), no jQuery or frameworks
* **Backend:**

  * **WordPress REST API** (custom endpoints for search, autosuggest, ratings)
  * **PHP/MySQL** (fully compatible with shared/managed hosting)
  * **Elementor integration:** both widget and shortcode supported
* **Performance-focused:**

  * Fast queries, selective asset loading, optional object caching/transients

---

## 🛠️ Installation

1. Upload the plugin folder to `wp-content/plugins/`.
2. Activate **Popular AI Software Search** in WP Admin.
3. Add `[popular_ai_software_search]` to any post or page (or use the Elementor widget).
4. (Coming soon) Configure plugin options in WP Admin → Popular AI Search.

---

## 🏗️ Roadmap

* [x] Scaffold plugin structure, enqueue Vanilla JS, and register shortcode
* [x] Implement REST API endpoints (search, autosuggest, rating)
* [x] Build Vanilla JS-powered search and results UI
* [ ] Build admin settings page
* [ ] Integrate star rating and comments
* [ ] Add Elementor widget support
* [ ] Optimize for large datasets (indexing, caching)
* [ ] Polish, docs, and testing

---

## 📁 Folder Structure

```
/popular-ai-software-search/
  /admin/                # Admin settings, UI (coming soon)
  /assets/
    /css/                # Plugin styles
    /js/                 # Vanilla JS modules (search.js, etc.)
  /includes/             # REST endpoints, core plugin logic
  popular-ai-software-search.php
  readme.md
  changelog.md
```

---

## 💬 Technical Highlights (For Employers & Future You)

* **Modern best practices:** WP REST API, custom endpoints, secure, zero dependencies.
* **Vanilla JS for frontend:** Fast, maintainable, no dependency conflicts.
* **Optimized for shared hosting:** No Node.js, external servers, or CLI required.
* **Extendable:** Can be the foundation for advanced post directories, review sites, SaaS apps, or knowledge bases.

---

## 🤝 Credits

Developed by Ronald Allan Rivera
Portfolio: [allanwebdesign.com](https://allanwebdesign.com)

---

## 📝 License

MIT License (or GPL v2 if distributing in WordPress repo).