document.addEventListener('DOMContentLoaded', function() {
    // Get all relevant elements by ID
    const root = document.getElementById('pais-search-root');
    if (!root) return;

    const keywordInput = document.getElementById('pais-keyword');
    const categorySelect = document.getElementById('pais-category');
    const searchBtn = document.getElementById('pais-search-btn');
    const resultsDiv = document.getElementById('pais-results');
    const autosuggestDiv = document.getElementById('pais-autosuggest');

    // Sorting state (global to this file)
    let currentPage = 1;
    let maxPages = 1;
    let selectedView = 'grid';
    let paisLastSortBy = 'date';
    let paisLastSortOrder = 'desc';
    let lastData = null;

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
                    autosuggestDiv.querySelectorAll('li').forEach(li => {
                        li.addEventListener('click', () => {
                            keywordInput.value = li.textContent;
                            autosuggestDiv.innerHTML = '';
                            fetchResults(1, paisLastSortBy, paisLastSortOrder);
                        });
                    });
                });
        }, 250);
    });

    // --- SEARCH BUTTON ---
    searchBtn.addEventListener('click', function() { fetchResults(1, paisLastSortBy, paisLastSortOrder); });

    // --- SEARCH ON ENTER KEY ---
    keywordInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchResults(1, paisLastSortBy, paisLastSortOrder);
            autosuggestDiv.innerHTML = '';
        }
    });

    // --- CATEGORY CHANGE ---
    categorySelect.addEventListener('change', function() { fetchResults(1, paisLastSortBy, paisLastSortOrder); });

    // --- VIEW TOGGLE (Grid/List) ---
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
            fetchResults(currentPage - 1, paisLastSortBy, paisLastSortOrder);
        }
    };
    nextBtn.onclick = function() {
        if (currentPage < maxPages) {
            fetchResults(currentPage + 1, paisLastSortBy, paisLastSortOrder);
        }
    };

    // --- RESULTS RENDERING (now with sortable columns) ---
    function renderResults(data) {
        if (!data || !data.posts || !data.posts.length) {
            resultsDiv.innerHTML = '<div>No results found.</div>';
            return;
        }
        if (selectedView === 'list') {
            // Arrows for sort indication
            const arrow = (field) => {
                if (paisLastSortBy === field) {
                    return paisLastSortOrder === 'asc' ? ' ▲' : ' ▼';
                }
                return '';
            };
            resultsDiv.innerHTML = `
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th data-sort="title" style="cursor:pointer">Title${arrow('title')}</th>
                            <th>Summary</th>
                            <th data-sort="category" style="cursor:pointer">Categories${arrow('category')}</th>
                            <th data-sort="date" style="cursor:pointer">Date${arrow('date')}</th>
                            <th>Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.posts.map(post => `
                            <tr>
                                <td>${post.title}</td>
                                <td>${post.excerpt}</td>
                                <td>${post.category || ''}</td>
                                <td>${post.date}</td>
                                <td><a href="${post.permalink}" target="_blank">Learn More</a></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            // Add sorting handlers
            resultsDiv.querySelectorAll('th[data-sort]').forEach(th => {
                th.onclick = function() {
                    const sortBy = this.getAttribute('data-sort');
                    if (paisLastSortBy === sortBy) {
                        paisLastSortOrder = (paisLastSortOrder === 'asc' ? 'desc' : 'asc');
                    } else {
                        paisLastSortBy = sortBy;
                        paisLastSortOrder = 'asc';
                    }
                    fetchResults(1, paisLastSortBy, paisLastSortOrder);
                };
            });
        } else {
            resultsDiv.innerHTML = data.posts.map(post => `
                <div class="pais-result-item">
                    <h3><a href="${post.permalink}" target="_blank">${post.title}</a></h3>
                    <div>${post.excerpt}</div>
                    <div>${post.category ? post.category : ''} | ${post.date}</div>
                    <a href="${post.permalink}" target="_blank">Learn More</a>
                </div>
            `).join('');
        }
    }

    // --- FETCH RESULTS, SUPPORTS SORT/PAGINATION ---
    function fetchResults(page = 1, sortBy = 'date', sortOrder = 'desc') {
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

    // --- Fetch First Results On Load ---
    fetchResults(1, paisLastSortBy, paisLastSortOrder);
});
