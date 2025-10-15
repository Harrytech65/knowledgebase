<?php
/**
 * Knowledge Base Theme Functions with License System
 */

require_once get_template_directory() . '/functions-license.php';

// Theme Setup (Only if licensed)
function kb_theme_setup() {
    // Check license before enabling theme features
    if (!is_plugin_activated5929()) {
        return;
    }
    
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'knowledge-base'),
    ));
}
add_action('after_setup_theme', 'kb_theme_setup');

// Enqueue Styles and Scripts (Only if licensed)
function kb_theme_scripts() {
    wp_enqueue_style('kb-style', get_stylesheet_uri(), array(), '1.0');
    wp_enqueue_script('jquery');
    wp_enqueue_script('kb-scripts', get_template_directory_uri() . '/js/script.js', array('jquery'), time(), true);
    
    // Localize AJAX
    wp_localize_script('kb-scripts', 'kbAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('kb_nonce')
    ));
    
    wp_enqueue_media();
}
add_action('wp_enqueue_scripts', 'kb_theme_scripts');

// Block frontend if not licensed
add_action('template_redirect', 'kb_check_license_frontend');
function kb_check_license_frontend() {
    if (!is_plugin_activated5929() && !is_admin()) {
        wp_die(
            '<div style="text-align: center; padding: 100px 20px; font-family: -apple-system, sans-serif;">
                <h1 style="font-size: 48px; margin-bottom: 20px;">🔒</h1>
                <h2 style="color: #1f2937; margin-bottom: 15px;">Theme Not Activated</h2>
                <p style="color: #6b7280; font-size: 18px; margin-bottom: 30px;">Please activate your license to use this theme.</p>
                <a href="' . admin_url('options-general.php?page=license-validator5929') . '" style="display: inline-block; padding: 12px 30px; background: #2563eb; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">Activate License</a>
            </div>',
            'Theme License Required',
            ['response' => 403]
        );
    }
}

// Admin Scripts
function kb_admin_scripts($hook) {
    if (strpos($hook, 'kb-') !== false) {
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}
add_action('admin_enqueue_scripts', 'kb_admin_scripts');

/**
 * Knowledge Base Theme Functions - Visibility Control Only
 */

// Admin Menu - ONLY Visibility Control
function kb_add_admin_menu() {
    add_menu_page(
        'Knowledge Base Settings',
        'KB Settings',
        'manage_options',
        'kb-visibility',
        'kb_visibility_page',
        'dashicons-book',
        30
    );
}
add_action('admin_menu', 'kb_add_admin_menu');

// Visibility Control Page
function kb_visibility_page() {
    if (!current_user_can('manage_options')) return;
    
    if (isset($_POST['kb_save_visibility'])) {
        check_admin_referer('kb_visibility_nonce');
        
        $hidden_posts = isset($_POST['kb_hidden_posts']) ? array_map('intval', $_POST['kb_hidden_posts']) : array();
        $hidden_categories = isset($_POST['kb_hidden_categories']) ? array_map('intval', $_POST['kb_hidden_categories']) : array();
        
        update_option('kb_hidden_posts', $hidden_posts);
        update_option('kb_hidden_categories', $hidden_categories);
        
        echo '<div class="notice notice-success"><p>✅ Visibility settings saved!</p></div>';
    }
    
    $hidden_posts = get_option('kb_hidden_posts', array());
    $hidden_categories = get_option('kb_hidden_categories', array());
    
    $posts = get_posts(array('numberposts' => -1, 'post_status' => 'publish'));
    $categories = get_categories(array('hide_empty' => false));
    ?>
    
    <div class="wrap">
        <h1>👁️ Visibility Control</h1>
        <p style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">
            Control which posts and categories are visible in your Knowledge Base.
        </p>
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_visibility_nonce'); ?>
            
            <div class="kb-admin-panel">
                <div class="kb-admin-section">
                    <h2>📄 Hide Posts</h2>
                    <p>Select posts you want to hide from the Knowledge Base:</p>
                    
                    <?php if (empty($posts)): ?>
                        <p style="color: #6b7280; font-style: italic;">No posts found. Create some posts first!</p>
                    <?php else: ?>
                        <div style="max-height: 400px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; background: #f9fafb;">
                            <?php foreach ($posts as $post): ?>
                                <div class="kb-visibility-toggle">
                                    <label style="display: flex; align-items: center; padding: 8px; cursor: pointer; transition: background 0.2s;">
                                        <input type="checkbox" 
                                               name="kb_hidden_posts[]" 
                                               value="<?php echo esc_attr($post->ID); ?>"
                                               <?php checked(in_array($post->ID, $hidden_posts)); ?>
                                               style="margin-right: 10px;">
                                        <span style="flex: 1;"><?php echo esc_html($post->post_title); ?></span>
                                        <span style="font-size: 12px; color: #6b7280;">
                                            <?php 
                                            $cats = get_the_category($post->ID);
                                            if (!empty($cats)) {
                                                echo '(' . esc_html($cats[0]->name) . ')';
                                            }
                                            ?>
                                        </span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="kb-admin-section" style="margin-top: 40px;">
                    <h2>📁 Hide Categories</h2>
                    <p>Select categories you want to hide from the Knowledge Base:</p>
                    
                    <?php if (empty($categories)): ?>
                        <p style="color: #6b7280; font-style: italic;">No categories found. Create some categories first!</p>
                    <?php else: ?>
                        <div style="max-height: 400px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; background: #f9fafb;">
                            <?php foreach ($categories as $category): ?>
                                <div class="kb-visibility-toggle">
                                    <label style="display: flex; align-items: center; padding: 8px; cursor: pointer; transition: background 0.2s;">
                                        <input type="checkbox" 
                                               name="kb_hidden_categories[]" 
                                               value="<?php echo esc_attr($category->term_id); ?>"
                                               <?php checked(in_array($category->term_id, $hidden_categories)); ?>
                                               style="margin-right: 10px;">
                                        <span style="flex: 1;"><?php echo esc_html($category->name); ?></span>
                                        <span style="background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                            <?php echo esc_html($category->count); ?> posts
                                        </span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <p class="submit" style="margin-top: 30px;">
                    <button type="submit" name="kb_save_visibility" class="button button-primary button-large">
                        💾 Save Visibility Settings
                    </button>
                </p>
            </div>
        </form>
    </div>
    
    <style>
        .kb-admin-panel {
            background: white;
            padding: 30px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .kb-admin-section h2 {
            font-size: 18px;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .kb-visibility-toggle label:hover {
            background: #f3f4f6;
            border-radius: 6px;
        }
        
        .kb-visibility-toggle input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
    </style>
    <?php
}

// Apply Custom Styles (Simplified - using defaults only)
function kb_apply_custom_styles() {
    if (!is_plugin_activated5929()) {
        return;
    }
    ?>
    <style>
        :root {
            --kb-primary-color: #2563eb;
            --kb-secondary-color: #10b981;
            --kb-text-color: #1f2937;
            --kb-bg-color: #ffffff;
            --kb-card-bg: #f9fafb;
            --kb-border-color: #e5e7eb;
            --kb-gradient-start: #2563eb;
            --kb-gradient-end: #7c3aed;
        }
    </style>
    <?php
}
add_action('wp_head', 'kb_apply_custom_styles');

// Helper Functions
function kb_is_visible($item_id, $type = 'post') {
    $hidden_items = get_option($type === 'post' ? 'kb_hidden_posts' : 'kb_hidden_categories', array());
    return !in_array($item_id, $hidden_items);
}

function kb_get_visible_categories() {
    $categories = get_categories(array('hide_empty' => false));
    $visible_categories = array();
    
    foreach ($categories as $category) {
        if (kb_is_visible($category->term_id, 'category')) {
            $visible_categories[] = $category;
        }
    }
    
    return $visible_categories;
}

// ============================================
// AJAX FUNCTIONS - MUST BE AT THE END
// ============================================

// AJAX: Get posts by category
function kb_ajax_get_posts() {
    check_ajax_referer('kb_nonce', 'nonce');
    
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    
    $args = array(
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );
    
    if ($category_id > 0) {
        $args['cat'] = $category_id;
    }
    
    $posts = get_posts($args);
    $hidden_posts = get_option('kb_hidden_posts', array());
    
    $response = array();
    
    foreach ($posts as $post) {
        if (!in_array($post->ID, $hidden_posts)) {
            $categories = wp_get_post_categories($post->ID, array('fields' => 'names'));
            $response[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'excerpt' => wp_trim_words(get_the_excerpt($post->ID), 20),
                'link' => get_permalink($post->ID),
                'category' => !empty($categories) ? $categories[0] : 'Uncategorized'
            );
        }
    }
    
    wp_send_json_success($response);
}
add_action('wp_ajax_kb_get_posts', 'kb_ajax_get_posts');
add_action('wp_ajax_nopriv_kb_get_posts', 'kb_ajax_get_posts');

// AJAX: Search posts
function kb_ajax_search_posts() {
    check_ajax_referer('kb_nonce', 'nonce');

    $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    if (empty($search_query)) {
        wp_send_json_error('Empty search query');
    }

    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 20,
        's'              => $search_query
    );

    $search_results = new WP_Query($args);
    $hidden_posts = get_option('kb_hidden_posts', array());
    $response = array();

    if ($search_results->have_posts()) {
        while ($search_results->have_posts()) {
            $search_results->the_post();
            $post_id = get_the_ID();

            if (!in_array($post_id, $hidden_posts)) {
                $categories = wp_get_post_terms($post_id, 'category', array('fields' => 'names'));
                $response[] = array(
                    'id'       => $post_id,
                    'title'    => get_the_title(),
                    'excerpt'  => wp_trim_words(get_the_excerpt(), 20),
                    'link'     => get_permalink(),
                    'category' => !empty($categories) ? implode(', ', $categories) : 'Uncategorized',
                );
            }
        }
        wp_reset_postdata();
    }

    wp_send_json_success($response);
}

add_action('wp_ajax_kb_search', 'kb_ajax_search_posts');
add_action('wp_ajax_nopriv_kb_search', 'kb_ajax_search_posts');
function kb_enqueue_scripts() {
    wp_enqueue_script(
        'kb-script',
        get_template_directory_uri() . '/js/script.js', // adjust path if needed
        array('jquery'),
        '1.0',
        true
    );

    wp_localize_script('kb-script', 'kbAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('kb_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'kb_enqueue_scripts');
function kb_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'kb_excerpt_length');

// Remove [...] from excerpt
function kb_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'kb_excerpt_more');