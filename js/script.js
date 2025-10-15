jQuery(document).ready(function($) {
    
    // ============================================
    // SEARCH FUNCTIONALITY
    // ============================================
    
    const searchForm = $('#kb-search-form');
    const searchInput = $('#kb-search-input');
    const searchResults = $('#kb-search-results');
    let searchTimeout;

    // Real-time search as user types
    searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();

        if (query.length < 2) {
            searchResults.hide().html('');
            return;
        }

        // Debounce search - wait 500ms after user stops typing
        searchTimeout = setTimeout(function() {
            performSearch(query);
        }, 500);
    });

    // Handle form submission
    searchForm.on('submit', function(e) {
        e.preventDefault();
        const query = searchInput.val().trim();
        
        if (query.length >= 2) {
            performSearch(query);
        }
    });

    // Perform AJAX search
    function performSearch(query) {
        searchResults.html('<p style="text-align: center; color: #6b7280;">Searching...</p>').show();

        $.ajax({
            url: kbAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'kb_search',
                nonce: kbAjax.nonce,
                search: query
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    displaySearchResults(response.data);
                } else {
                    searchResults.html('<p style="text-align: center; color: #6b7280;">No articles found for "' + query + '"</p>');
                }
            },
            error: function() {
                searchResults.html('<p style="text-align: center; color: #ef4444;">Error performing search. Please try again.</p>');
            }
        });
    }

    // Display search results with clickable links
    function displaySearchResults(results) {
        let html = '<div style="max-height: 400px; overflow-y: auto;">';
        html += '<h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">Search Results (' + results.length + ')</h3>';

        results.forEach(function(post) {
            html += '<div style="padding: 15px; margin-bottom: 10px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; cursor: pointer; transition: all 0.2s;" class="search-result-item" data-link="' + post.link + '">';
            html += '<h4 style="font-size: 16px; font-weight: 600; margin-bottom: 5px; color: #2563eb;">' + post.title + '</h4>';
            html += '<p style="font-size: 14px; color: #6b7280; margin-bottom: 8px;">' + post.excerpt + '</p>';
            html += '<span style="font-size: 12px; color: #9ca3af;">📁 ' + post.category + '</span>';
            html += '</div>';
        });

        html += '</div>';
        searchResults.html(html);

        // Add click event to each search result
        $('.search-result-item').on('click', function() {
            const link = $(this).data('link');
            window.location.href = link;
        });

        // Add hover effect
        $('.search-result-item').hover(
            function() {
                $(this).css({
                    'background': '#ffffff',
                    'border-color': '#2563eb',
                    'transform': 'translateY(-2px)',
                    'box-shadow': '0 4px 6px rgba(0,0,0,0.1)'
                });
            },
            function() {
                $(this).css({
                    'background': '#f9fafb',
                    'border-color': '#e5e7eb',
                    'transform': 'translateY(0)',
                    'box-shadow': 'none'
                });
            }
        );
    }

    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#kb-search-form, #kb-search-results').length) {
            searchResults.hide();
        }
    });

    // ============================================
    // CATEGORY TABS FUNCTIONALITY
    // ============================================
    
    $('.kb-tab').on('click', function() {
        const categoryId = $(this).data('category');
        
        // Update active tab
        $('.kb-tab').removeClass('active');
        $(this).addClass('active');

        if (categoryId === 'all') {
            // Show all cards
            $('.kb-card').fadeIn(300);
        } else {
            // Filter posts by category via AJAX
            filterByCategory(categoryId);
        }
    });

    function filterByCategory(categoryId) {
        const cardsGrid = $('#kb-cards-grid');
        cardsGrid.html('<p style="text-align: center; padding: 40px; color: #6b7280;">Loading...</p>');

        $.ajax({
            url: kbAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'kb_get_posts',
                nonce: kbAjax.nonce,
                category_id: categoryId
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    displayCategoryPosts(response.data, cardsGrid);
                } else {
                    cardsGrid.html('<p style="text-align: center; padding: 40px; color: #6b7280;">No articles found in this category.</p>');
                }
            },
            error: function() {
                cardsGrid.html('<p style="text-align: center; padding: 40px; color: #ef4444;">Error loading posts.</p>');
            }
        });
    }

    function displayCategoryPosts(posts, container) {
        let html = '';
        
        posts.forEach(function(post) {
            html += '<div class="kb-card" onclick="location.href=\'' + post.link + '\'">';
            html += '<div class="kb-card-icon">📄</div>';
            html += '<h3 class="kb-card-title">' + post.title + '</h3>';
            html += '<p class="kb-card-description">' + post.excerpt + '</p>';
            html += '<span class="kb-card-count">Read more →</span>';
            html += '</div>';
        });

        container.html(html);
    }

    // ============================================
    // SMOOTH SCROLL FOR ANCHOR LINKS
    // ============================================
    
    $('a[href^="#"]').on('click', function(e) {
        const target = $(this.getAttribute('href'));
        
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 600);
        }
    });

});