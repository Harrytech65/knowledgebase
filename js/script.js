jQuery(document).ready(function($) {
    
    // Category Tabs Click Handler
    $('.kb-tab').on('click', function() {
        const categoryId = $(this).data('category');
        
        // Update active tab
        $('.kb-tab').removeClass('active');
        $(this).addClass('active');
        
        if (categoryId === 'all') {
            // Show all cards
            $('.kb-card').show();
        } else {
            // Filter posts by category via AJAX
            $.ajax({
                url: kbAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'kb_get_posts',
                    category_id: categoryId,
                    nonce: kbAjax.nonce
                },
                beforeSend: function() {
                    $('#kb-cards-grid').html('<div style="grid-column: 1/-1; text-align: center; padding: 40px;"><p>Loading...</p></div>');
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '';
                        
                        response.data.forEach(function(post) {
                            html += `
                                <div class="kb-card" onclick="location.href='${post.link}'">
                                    <div class="kb-card-icon">📄</div>
                                    <h3 class="kb-card-title">${post.title}</h3>
                                    <p class="kb-card-description">${post.excerpt}</p>
                                    <span class="kb-card-count">Read more →</span>
                                </div>
                            `;
                        });
                        
                        $('#kb-cards-grid').html(html);
                    } else {
                        $('#kb-cards-grid').html('<div style="grid-column: 1/-1; text-align: center; padding: 40px;"><p>No articles found in this category.</p></div>');
                    }
                },
                error: function() {
                    $('#kb-cards-grid').html('<div style="grid-column: 1/-1; text-align: center; padding: 40px;"><p>Error loading articles. Please try again.</p></div>');
                }
            });
        }
    });
    
    // Search Functionality
    let searchTimeout;
    
    $('#kb-search-input').on('keyup', function() {
        const searchQuery = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (searchQuery.length < 2) {
            $('#kb-search-results').hide();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            performSearch(searchQuery);
        }, 300);
    });
    
    // Search Form Submit
    $('#kb-search-form').on('submit', function(e) {
        e.preventDefault();
        const searchQuery = $('#kb-search-input').val().trim();
        
        if (searchQuery.length >= 2) {
            performSearch(searchQuery);
        }
    });
    
    // Perform Search via AJAX
    function performSearch(query) {
        $.ajax({
            url: kbAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'kb_search',
                search: query,
                nonce: kbAjax.nonce
            },
            beforeSend: function() {
                $('#kb-search-results').html('<p style="text-align: center;">Searching...</p>').show();
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    let html = '<h3 style="margin-bottom: 15px; font-size: 18px;">Search Results</h3>';
                    
                    response.data.forEach(function(post) {
                        html += `
                            <div style="padding: 15px; border-bottom: 1px solid var(--kb-border-color); cursor: pointer;" onclick="location.href='${post.link}'">
                                <h4 style="font-size: 16px; margin-bottom: 5px; color: var(--kb-primary-color);">${post.title}</h4>
                                <p style="font-size: 14px; color: #6b7280; margin: 0;">${post.excerpt}</p>
                            </div>
                        `;
                    });
                    
                    $('#kb-search-results').html(html).show();
                } else {
                    $('#kb-search-results').html('<p style="text-align: center; color: #6b7280;">No results found for "' + query + '"</p>').show();
                }
            },
            error: function() {
                $('#kb-search-results').html('<p style="text-align: center; color: #ef4444;">Error performing search. Please try again.</p>').show();
            }
        });
    }
    
    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.kb-search-wrapper').length) {
            $('#kb-search-results').hide();
        }
    });
    
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        const target = $(this.getAttribute('href'));
        
        if (target.length) {
            e.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 600);
        }
    });
    
    // Add animation to cards on hover
    $('.kb-card').hover(
        function() {
            $(this).css('transform', 'translateY(-5px)');
        },
        function() {
            $(this).css('transform', 'translateY(0)');
        }
    );
    
    // Feedback buttons
    $('button').filter(function() {
        return $(this).text().includes('👍') || $(this).text().includes('👎');
    }).on('click', function() {
        const isHelpful = $(this).text().includes('👍');
        const message = isHelpful ? 'Thank you for your feedback! 😊' : 'Thanks! We\'ll work on improving this article.';
        
        $(this).parent().html(`<p style="color: var(--kb-primary-color); font-weight: 500;">${message}</p>`);
    });
});