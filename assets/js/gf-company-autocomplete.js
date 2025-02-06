jQuery(document).ready(function($) {
    // Config
    const minChars = 2;
    const debounceTime = 300;
    const CACHE_PREFIX = 'gf_openkvk_cache_';
    const CACHE_EXPIRY = 24 * 60 * 60 * 1000; // 24 hours in milliseconds
    let debounceTimer;

    // Find the company input field
    const companyField = $('.gf-openkvk-autocomplete');
    const suggestionsContainer = $('.gf-openkvk-suggestions');

    // Cache management functions
    const cache = {
        set: function(query, data) {
            try {
                localStorage.setItem(CACHE_PREFIX + query, JSON.stringify({
                    timestamp: Date.now(),
                    data: data
                }));
            } catch (e) {
                // Handle localStorage errors (e.g., quota exceeded)
                this.cleanup();
            }
        },
        get: function(query) {
            const item = localStorage.getItem(CACHE_PREFIX + query);
            if (!item) return null;

            const cached = JSON.parse(item);
            if (Date.now() - cached.timestamp > CACHE_EXPIRY) {
                localStorage.removeItem(CACHE_PREFIX + query);
                return null;
            }
            return cached.data;
        },
        cleanup: function() {
            // Remove expired items and oldest items if storage is full
            Object.keys(localStorage)
                .filter(key => key.startsWith(CACHE_PREFIX))
                .forEach(key => {
                    try {
                        const cached = JSON.parse(localStorage.getItem(key));
                        if (Date.now() - cached.timestamp > CACHE_EXPIRY) {
                            localStorage.removeItem(key);
                        }
                    } catch (e) {
                        localStorage.removeItem(key);
                    }
                });
        }
    };

    // Handle input changes
    companyField.on('input', function() {
        const query = $(this).val();
        clearTimeout(debounceTimer);

        if (query.length < minChars) {
            suggestionsContainer.hide();
            return;
        }

        // Show loading state
        suggestionsContainer.html('<li class="gf-openkvk-loading">Zoeken...</li>').show();

        // Check cache first
        const cachedResults = cache.get(query);
        if (cachedResults) {
            displaySuggestions(cachedResults);
            return;
        }

        // Debounce the API call
        debounceTimer = setTimeout(() => {
            fetchSuggestions(query);
        }, debounceTime);
    });

    // Handle clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.gf-openkvk-autocomplete-wrapper').length) {
            suggestionsContainer.hide();
        }
    });

    // Fetch suggestions from the API
    function fetchSuggestions(query) {
        $.ajax({
            url: gf_openkvk_autocomplete.ajax_url,
            type: 'GET',
            data: {
                action: 'gf_openkvk_autocomplete',
                nonce: gf_openkvk_autocomplete.nonce,
                q: query
            },
            success: function(response) {
                if (response.success && response.data) {
                    // Store in cache
                    cache.set(query, response.data);
                    displaySuggestions(response.data);
                } else {
                    showNoResults();
                }
            },
            error: function() {
                showNoResults();
            }
        });
    }

    // Display suggestions in the dropdown
    function displaySuggestions(suggestions) {
        if (!suggestions.length) {
            showNoResults();
            return;
        }

        const html = suggestions.map(company => `
            <li class="gf-openkvk-suggestion" data-company='${JSON.stringify(company)}'>
                <div class="gf-openkvk-suggestion-name">${company.handelsnaam}</div>
                <div class="gf-openkvk-suggestion-location">${company.plaats}</div>
            </li>
        `).join('');

        suggestionsContainer.html(html).show();

        // Handle suggestion clicks
        $('.gf-openkvk-suggestion').on('click', function() {
            const company = $(this).data('company');
            companyField.val(company.handelsnaam);
            suggestionsContainer.hide();
            
            // Trigger change event for Gravity Forms
            companyField.trigger('change');
        });
    }

    // Show no results message
    function showNoResults() {
        suggestionsContainer.html('<li class="gf-openkvk-no-results">Geen resultaten gevonden</li>').show();
    }

    // Keyboard navigation
    companyField.on('keydown', function(e) {
        const suggestions = $('.gf-openkvk-suggestion');
        const active = $('.gf-openkvk-suggestion.active');
        
        switch(e.keyCode) {
            case 40: // Down arrow
                e.preventDefault();
                if (!active.length) {
                    suggestions.first().addClass('active');
                } else {
                    active.removeClass('active').next().addClass('active');
                }
                break;
                
            case 38: // Up arrow
                e.preventDefault();
                if (!active.length) {
                    suggestions.last().addClass('active');
                } else {
                    active.removeClass('active').prev().addClass('active');
                }
                break;
                
            case 13: // Enter
                e.preventDefault();
                if (active.length) {
                    active.click();
                }
                break;
                
            case 27: // Escape
                suggestionsContainer.hide();
                break;
        }
    });

    // Clean up expired cache items on page load
    cache.cleanup();
}); 