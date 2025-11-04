document.addEventListener('DOMContentLoaded', function() {
    // Setup for desktop search form
    setupSearch('search-input', 'search-hints', 'live-search');
    
    // Setup for mobile search form
    setupSearch('mobile-search-input', 'mobile-search-hints', 'mobile-live-search');
    
    // Setup dynamic search if we're on the search page
    setupDynamicSearch();
    
    function setupSearch(inputId, hintsId, formId) {
        const searchInput = document.getElementById(inputId);
        const searchHints = document.getElementById(hintsId);
        const searchForm = document.getElementById(formId);
        
        if (!searchInput || !searchHints || !searchForm) {
            console.error(`Search elements not found for ${inputId}`);
            return;
        }
        
        // Debounce function to limit API calls
        function debounce(func, delay) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), delay);
            };
        }
        
        // Escape regex special characters
        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        
        // Highlight matching text
        function highlightMatch(text, query) {
            if (!query) return text;
            const escapedQuery = escapeRegExp(query);
            const regex = new RegExp(`(${escapedQuery})`, 'gi');
            return text.replace(regex, '<span class="highlight">$1</span>');
        }
        
        // Format price with currency
        function formatPrice(price) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2
            }).format(price);
        }
        
        // Function to perform the search
        const performSearch = debounce(function() {
            const query = searchInput.value.trim();
            
            // Clear results if empty query
            if (query.length === 0) {
                searchHints.innerHTML = '';
                searchHints.classList.add('d-none');
                return;
            }
            
            if (query.length >= 2) {
                // Use the correct endpoint for your AJAX search
                fetch(`${SITE_URL}products/ajax-search?query=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        renderSearchResults(data, query);
                        
                        // If we're on the search page, also update the main content
                        if (window.location.href.includes('products/search')) {
                            updateMainSearchResults(query, data);
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            } else {
                searchHints.innerHTML = '';
                searchHints.classList.add('d-none');
            }
        }, 300);
        
        // Render search results in dropdown
        function renderSearchResults(data, query) {
            // Clear previous results
            searchHints.innerHTML = '';
            
            // Get results array from response object
            const results = data.results || [];
            const totalCount = data.totalCount || 0;
            
            if (results.length === 0) {
                // No results found
                const noResults = document.createElement('div');
                noResults.className = 'search-hint-item search-no-results';
                noResults.textContent = `No products found matching "${query}"`;
                searchHints.appendChild(noResults);
                searchHints.classList.remove('d-none');
                return;
            }
            
            // Create header - show total count if there are more results than shown
            let headerText = `Search results for "${query}"`;
            if (totalCount > results.length) {
                headerText += ` (showing ${results.length} of ${totalCount})`;
            }
            
            let html = `<div class="search-hint-header">${headerText}</div>`;
            
            // Create results
            results.forEach(product => {
                // Highlight matching text
                const highlightedName = highlightMatch(product.name, query);
                
                html += `
                <a href="${SITE_URL}products/detail/${product.id}" class="search-hint-item">
                    <div class="search-hint-image">
                        <img src="${SITE_URL}${product.image}" alt="${product.name}">
                    </div>
                    <div class="search-hint-content">
                        <div class="search-hint-title">${highlightedName}</div>
                        <div class="search-hint-price">${formatPrice(product.price)}</div>
                    </div>
                </a>
                `;
            });
            
            // Add footer with "View all results" link
            html += `
            <div class="search-hint-footer">
                <a href="${SITE_URL}products/search?search=${encodeURIComponent(query)}" class="search-all-btn">
                    View all results
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            `;
            
            // Add results to dropdown and show it
            searchHints.innerHTML = html;
            searchHints.classList.remove('d-none');
        }
        
        // Event listeners
        searchInput.addEventListener('input', performSearch);
        
        // Show results when input is focused and has value
        searchInput.addEventListener('focus', function() {
            if (searchInput.value.trim().length >= 2) {
                performSearch();
            }
        });
        
        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchHints.contains(e.target)) {
                searchHints.classList.add('d-none');
            }
        });
        
        // Form submission
        searchForm.addEventListener('submit', function(e) {
            const query = searchInput.value.trim();
            if (query.length === 0) {
                e.preventDefault();
            }
        });
    }
    
    // Setup for updating the main content on the search page
    function setupDynamicSearch() {
        // Check if we're on the search page
        if (window.location.href.includes('products/search')) {
            // Get search input and results container
            const searchInput = document.querySelector('.dynamic-search-input');
            const searchResultsContainer = document.getElementById('dynamic-search-results');
            
            if (searchInput && searchResultsContainer) {
                // Debounce function
                function debounce(func, delay) {
                    let timeout;
                    return function() {
                        const context = this;
                        const args = arguments;
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(context, args), delay);
                    };
                }
                
                // Function to update the main search results
                const updateResults = debounce(function() {
                    const query = searchInput.value.trim();
                    
                    if (query.length >= 2) {
                        // Update browser URL without reloading page
                        const url = new URL(window.location.href);
                        url.searchParams.set('search', query);
                        window.history.replaceState({}, '', url);
                        
                        // Show loading indicator
                        searchResultsContainer.innerHTML = '<div class="text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                        
                        // Fetch search results - add all=true parameter
                        fetch(`${SITE_URL}products/ajax-search?query=${encodeURIComponent(query)}&all=true`)
                            .then(response => response.json())
                            .then(data => {
                                updateMainSearchResults(query, data);
                            })
                            .catch(error => {
                                console.error('Error updating search results:', error);
                                searchResultsContainer.innerHTML = '<div class="alert alert-danger">Error loading search results. Please try again.</div>';
                            });
                    } else if (query.length === 0) {
                        // Show empty search message
                        searchResultsContainer.innerHTML = '<div class="alert alert-info">Please enter at least 2 characters to search.</div>';
                    }
                }, 500);
                
                // Attach event listener
                searchInput.addEventListener('input', updateResults);
                
                // Initial update if search parameter exists
                if (searchInput.value.trim().length >= 2) {
                    updateResults();
                }
            }
        }
    }
    
    // Function to update the main search results
    function updateMainSearchResults(query, data) {
        const searchResultsContainer = document.getElementById('dynamic-search-results');
        if (!searchResultsContainer) return;
        
        // Update page title
        document.querySelector('h1').textContent = `Search Results for "${query}"`;
        document.title = `Search: ${query} - Pet Shop`;
        
        // Extract results array from response object
        const products = data.results || [];
        const totalCount = data.totalCount || 0;
        
        if (products.length === 0) {
            searchResultsContainer.innerHTML = `
                <div class="alert alert-info">
                    <h4 class="alert-heading">No products found</h4>
                    <p>We couldn't find any products matching "${query}".</p>
                    <hr>
                    <p class="mb-0">Try using different keywords or check out our <a href="${SITE_URL}products">product catalog</a>.</p>
                </div>
            `;
            return;
        }
        
        // Build HTML for products
        let html = '<div class="product-container d-flex flex-wrap">';
        
        products.forEach(product => {
            // Highlight matching text
            let name = product.name;
            if (query) {
                const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                name = name.replace(regex, '<span class="highlight">$1</span>');
            }
            
            html += `
                <div class="product-col">
                    <a href="${SITE_URL}products/detail/${product.id}" class="text-decoration-none">
                        <div class="card product-card h-100 clickable-card">
                            <img src="${SITE_URL}${product.image}" class="card-img-top" alt="${product.name}">
                            <div class="card-body">
                                <h5 class="card-title">${name}</h5>
                                <p class="card-text">$${Number(product.price).toFixed(2)}</p>
                            </div>
                        </div>
                    </a>
                </div>
            `;
        });
        
        html += '</div>';
        
        // Update the results container
        searchResultsContainer.innerHTML = html;
    }
});