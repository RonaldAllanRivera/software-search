document.addEventListener('DOMContentLoaded', function() {
    // Get all relevant elements by ID
    const root = document.getElementById('pais-search-root');
    if (!root) return;

    const keywordInput = document.getElementById('pais-keyword');
    const categorySelect = document.getElementById('pais-category');
    const searchBtn = document.getElementById('pais-search-btn');
    const resultsDiv = document.getElementById('pais-results');
    const autosuggestDiv = document.getElementById('pais-autosuggest');

    // --- AUTOSUGGEST ---
    let autosuggestTimeout = null;
    keywordInput.addEventListener('input', function() {
        clearTimeout(autosuggestTimeout);
        const value = keywordInput.value.trim();
        autosuggestDiv.innerHTML = '';
        if (value.length < 2) return;
        autosuggestTimeout = setTimeout(() => {
            fetch(`${pais_vars.rest_url}popularai/v1/autosuggest?q=${encodeURIComponent(value)}`)
                .then(res => res.json())
                .then(words => {
                    if (!words.length) return;
                    autosuggestDiv.innerHTML = '<ul>' +
                        words.map(word => `<li style="cursor:pointer">${word}</li>`).join('') +
                        '</ul>';
                    // Add click events
                    autosuggestDiv.querySelectorAll('li').forEach(li => {
                        li.addEventListener('click', () => {
                            keywordInput.value = li.textContent;
                            autosuggestDiv.innerHTML = '';
                            fetchResults(1);
                        });
                    });
                });
        }, 250);
    });

    // --- SEARCH BUTTON ---
    searchBtn.addEventListener('click', function() { fetchResults(1); });

    // --- SEARCH ON ENTER KEY ---
    keywordInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchResults(1);
            autosuggestDiv.innerHTML = '';
        }
    });

    // --- CATEGORY CHANGE ---
    categorySelect.addEventListener('change', function() { fetchResults(1); });

    // --- VIEW TOGGLE (Grid/List) ---
    let selectedView = 'grid';
    // Insert view toggle UI after the search form
    const viewToggle = document.createElement('div');
    viewToggle.style.display = 'flex';
    viewToggle.style.gap = '0.5rem';
    viewToggle.style.margin = '0.5rem 0';
    viewToggle.innerHTML = `
        <button id="pais-view-grid" class="pais-view-btn active">Grid</button>
        <button id="pais-view-list" class="pais-view-btn">List</button>
    `;
    root.querySelector('#pais-search-form').after(viewToggle);
    document.getElementById('pais-view-grid').onclick = function() {
        selectedView = 'grid';
        this.classList.add('active');
        document.getElementById('pais-view-list').classList.remove('active');
        renderResults(lastData);
    };
    document.getElementById('pais-view-list').onclick = function() {
        selectedView = 'list';
        this.classList.add('active');
        document.getElementById('pais-view-grid').classList.remove('active');
        renderResults(lastData);
    };

    // --- PAGINATION ---
    let currentPage = 1;
    let maxPages = 1;
    // Insert pagination UI after the results div
    const pagination = document.createElement('div');
    pagination.id = 'pais-pagination';
    pagination.style.margin = '1rem 0';
    pagination.innerHTML = `
        <button id="pais-prev" disabled>Prev</button>
        <span id="pais-page-label"></span>
        <button id="pais-next" disabled>Next</button>
    `;
    root.appendChild(pagination);
    const prevBtn = document.getElementById('pais-prev');
    const nextBtn = document.getElementById('pais-next');
    const pageLabel = document.getElementById('pais-page-label');
    prevBtn.onclick = function() {
        if (currentPage > 1) {
            fetchResults(currentPage - 1);
        }
    };
    nextBtn.onclick = function() {
        if (currentPage < maxPages) {
            fetchResults(currentPage + 1);
        }
    };

    // --- RESULTS RENDERING ---
    let lastData = null;
    
    function isMobile() {
        return window.innerWidth <= 800;
    }
    
    function renderResults(data) {
        if (!data || !data.posts || !data.posts.length) {
            resultsDiv.innerHTML = '<div>No results found.</div>';
            return;
        }
        
        if (selectedView === 'list') {
            if (isMobile()) {
                // CARD LAYOUT FOR MOBILE
                resultsDiv.innerHTML = data.posts.map(post => `
                    <div class="pais-card">
                        <div class="pais-card-title">${post.title}</div>
                        <div class="pais-card-content">
                            <div><strong>Summary:</strong> ${post.excerpt}</div>
                            <div><strong>Categories:</strong> ${post.category || ''}</div>
                            <div><strong>Comments:</strong> ${typeof post.comments !== "undefined" ? post.comments : 0}</div>
                            <div><strong>Rating:</strong> ${
                                post.votes > 0
                                ? `<span title="Rated ${post.rating} out of 5 by ${post.votes} user(s)">
                                    <span class="pais-rating-star">${'★'.repeat(Math.round(post.rating))}</span>
                                    <span class="pais-rating-star pais-rating-star-empty">${'☆'.repeat(5 - Math.round(post.rating))}</span>
                                    <span class="pais-rating-count">(${post.votes})</span>
                                </span>`
                                : '—'
                            }</div>
                            <div><strong>Date:</strong> ${post.date}</div>
                            <div><a href="${post.permalink}" class="pais-button" target="_blank">Learn More</a></div>
                        </div>
                    </div>
                `).join('');
            } else {
                // TABLE LAYOUT FOR DESKTOP
                resultsDiv.innerHTML = `
                    <table class="pais-results-table">
                        <thead>
                            <tr>
                                <th class="pais-sort" data-sort="title">Title</th>
                                <th>Summary</th>
                                <th class="pais-sort" data-sort="category">Categories</th>
                                <th class="pais-sort" data-sort="comments">Comments</th>
                                <th class="pais-sort" data-sort="rating">Rating</th>
                                <th class="pais-sort" data-sort="date">Date</th>
                                <th>Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.posts.map(post => `
                                <tr>
                                    <td data-label="Title">${post.title}</td>
                                    <td data-label="Summary">${post.excerpt}</td>
                                    <td class="pais-nowrap" data-label="Categories">${post.category || ''}</td>
                                    <td class="pais-nowrap" data-label="Comments">${typeof post.comments !== "undefined" ? post.comments : 0}</td>
                                    <td class="pais-nowrap" data-label="Rating">
                                        ${post.votes > 0 
                                            ? `<span title="Rated ${post.rating} out of 5 by ${post.votes} user(s)">
                                                <span class="pais-rating-star">${'★'.repeat(Math.round(post.rating))}</span>
                                                <span class="pais-rating-star pais-rating-star-empty">${'☆'.repeat(5 - Math.round(post.rating))}</span>
                                                <span class="pais-rating-count">(${post.votes})</span>
                                            </span>`
                                            : '—'}
                                    </td>
                                    <td class="pais-nowrap" data-label="Date">${post.date}</td>
                                    <td data-label=""><a href="${post.permalink}" target="_blank">Learn More</a></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
                
                // Attach sort handlers (only for table)
                resultsDiv.querySelectorAll('.pais-sort').forEach(th => {
                    th.onclick = function() {
                        const sortBy = th.dataset.sort;
                        if (window.paisLastSortBy === sortBy) {
                            window.paisLastSortOrder = (window.paisLastSortOrder === 'asc' ? 'desc' : 'asc');
                        } else {
                            window.paisLastSortBy = sortBy;
                            window.paisLastSortOrder = 'desc';
                        }
                        fetchResults(1, window.paisLastSortBy, window.paisLastSortOrder);
                    };
                });
            }
        } else {
            // GRID VIEW
            resultsDiv.innerHTML = data.posts.map(post => `
                <div class="pais-result-item">
                    <h3><a href="${post.permalink}" target="_blank">${post.title}</a></h3>
                    <div>${post.excerpt}</div>
                    <div>${post.category ? post.category : ''} | ${post.date}</div>
                    <a href="${post.permalink}" class="pais-button" target="_blank">Learn More</a>
                </div>
            `).join('');
        }
    }

    // --- FETCH RESULTS WITH PAGINATION SUPPORT ---
    function fetchResults(page = 1, sortBy = window.paisLastSortBy, sortOrder = window.paisLastSortOrder) {
        const keyword = keywordInput.value.trim();
        const category = categorySelect.value;
        resultsDiv.innerHTML = 'Loading...';
        fetch(`${pais_vars.rest_url}popularai/v1/search?keyword=${encodeURIComponent(keyword)}&category=${encodeURIComponent(category)}&page=${page}&orderby=${encodeURIComponent(sortBy)}&order=${encodeURIComponent(sortOrder)}`)
            .then(res => res.json())
            .then(data => {
                lastData = data;
                currentPage = data.current_page || page;
                maxPages = data.max_num_pages || 1;
                renderResults(data);
                pageLabel.textContent = `Page ${currentPage} of ${maxPages}`;
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= maxPages;
            });
    }

    // --- LOAD CATEGORIES ---
    fetch(`${pais_vars.rest_url}wp/v2/categories?per_page=100`)
        .then(res => res.json())
        .then(data => {
            categorySelect.innerHTML = '<option value="">All Categories</option>' +
                data.map(cat => `<option value="${cat.slug}">${cat.name}</option>`).join('');
        });

    // --- OPTIONAL: Fetch First Results On Load ---
    fetchResults(1);
});
