<?php
/**
 * knowzard Theme Functions with License System & Full Customization
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
        
        // Custom admin CSS
        wp_add_inline_style('wp-color-picker', '
            .kb-customization-panel {
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                margin: 20px 0;
            }
            .kb-section {
                padding: 25px;
                border-bottom: 1px solid #e5e7eb;
            }
            .kb-section:last-child {
                border-bottom: none;
            }
            .kb-section h2 {
                font-size: 18px;
                margin: 0 0 20px 0;
                padding-bottom: 10px;
                border-bottom: 2px solid #2563eb;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .kb-form-row {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 20px;
                margin-bottom: 20px;
            }
            .kb-form-group {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .kb-form-group label {
                font-weight: 600;
                color: #374151;
                font-size: 14px;
            }
            .kb-form-group input[type="text"],
            .kb-form-group input[type="number"],
            .kb-form-group select {
                padding: 10px 15px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                font-size: 14px;
                transition: all 0.2s;
            }
            .kb-form-group input:focus,
            .kb-form-group select:focus {
                outline: none;
                border-color: #2563eb;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            }
            .kb-color-picker {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .kb-color-preview {
                width: 50px;
                height: 38px;
                border-radius: 6px;
                border: 2px solid #d1d5db;
                cursor: pointer;
            }
            .kb-upload-btn {
                padding: 8px 16px;
                background: #2563eb;
                color: white;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
                transition: background 0.2s;
            }
            .kb-upload-btn:hover {
                background: #1d4ed8;
            }
            .kb-logo-preview {
                margin-top: 10px;
                max-width: 200px;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                padding: 10px;
            }
            .kb-logo-preview img {
                max-width: 100%;
                height: auto;
            }
            .kb-submit-row {
                display: flex;
                gap: 15px;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 2px solid #e5e7eb;
            }
        ');
    }
}
add_action('admin_enqueue_scripts', 'kb_admin_scripts');

/**
 * Admin Menu - Visibility & Customization
 */
function kb_add_admin_menu() {
    // Main menu
    add_menu_page(
        'knowzard',
        'knowzard ',
        'manage_options',
        'kb-settings',
        'kb_visibility_page',
        'dashicons-superhero',
        30
    );
    
    // Visibility submenu
    add_submenu_page(
        'kb-settings',
        'Visibility Control',
        ' Visibility',
        'manage_options',
        'kb-settings',
        'kb_visibility_page'
    );
    
    // Customization submenu
    add_submenu_page(
        'kb-settings',
        'Customization',
        ' Customization',
        'manage_options',
        'kb-customization',
        'kb_customization_page'
    );
}
add_action('admin_menu', 'kb_add_admin_menu');

// Add new submenu for Post Manager
function kb_add_post_manager_menu() {
    add_submenu_page(
        'kb-settings',
        'Manage Posts & Categories',
        '📝 Post Manager',
        'manage_options',
        'kb-post-manager',
        'kb_post_manager_page'
    );
}
add_action('admin_menu', 'kb_add_post_manager_menu');
/**
 * Customization Page - With Live Preview
 */
function kb_customization_page() {
    if (!current_user_can('manage_options')) return;
    
    // Handle form submission
    if (isset($_POST['kb_save_customization'])) {
        check_admin_referer('kb_customization_nonce');
        
        // Save all customization options
        $options = array(
            // Colors
            'kb_primary_color' => sanitize_hex_color($_POST['kb_primary_color']),
            'kb_secondary_color' => sanitize_hex_color($_POST['kb_secondary_color']),
            'kb_text_color' => sanitize_hex_color($_POST['kb_text_color']),
            'kb_bg_color' => sanitize_hex_color($_POST['kb_bg_color']),
            'kb_card_bg' => sanitize_hex_color($_POST['kb_card_bg']),
            'kb_border_color' => sanitize_hex_color($_POST['kb_border_color']),
            'kb_gradient_start' => sanitize_hex_color($_POST['kb_gradient_start']),
            'kb_gradient_end' => sanitize_hex_color($_POST['kb_gradient_end']),
            
            // Logo
            'kb_logo_url' => esc_url_raw($_POST['kb_logo_url']),
            'kb_logo_height' => intval($_POST['kb_logo_height']),
            
            // Typography
            'kb_font_family' => sanitize_text_field($_POST['kb_font_family']),
            'kb_heading_font' => sanitize_text_field($_POST['kb_heading_font']),
            'kb_font_size' => intval($_POST['kb_font_size']),
            
            // Header
            'kb_header_bg' => sanitize_hex_color($_POST['kb_header_bg']),
            'kb_header_text' => sanitize_hex_color($_POST['kb_header_text']),
            
            // Buttons
            'kb_button_radius' => intval($_POST['kb_button_radius']),
            'kb_button_hover' => sanitize_hex_color($_POST['kb_button_hover']),

            // homepage
            'kb_header_title' => sanitize_text_field($_POST['kb_header_title']),
            'kb_header_description' => sanitize_text_field($_POST['kb_header_description']),
            'kb_search_placeholder' => sanitize_text_field($_POST['kb_search_placeholder']),
            'kb_breadcrumb_text' => sanitize_text_field($_POST['kb_breadcrumb_text']),  
            
            // Cards
            'kb_card_radius' => intval($_POST['kb_card_radius']),
            'kb_card_shadow' => sanitize_text_field($_POST['kb_card_shadow']),
            
            // Footer
            'kb_footer_bg' => sanitize_hex_color($_POST['kb_footer_bg']),
            'kb_footer_text' => sanitize_hex_color($_POST['kb_footer_text']),
        );
        
        foreach ($options as $key => $value) {
            update_option($key, $value);
        }
        
        echo '<div class="notice notice-success"><p>✅ Customization settings saved successfully!</p></div>';
    }
    
    // Handle reset to defaults
    if (isset($_POST['kb_reset_customization'])) {
        check_admin_referer('kb_customization_nonce');
        
        $defaults = kb_get_default_settings();
        foreach ($defaults as $key => $value) {
            update_option($key, $value);
        }
        
        echo '<div class="notice notice-success"><p>✅ Settings reset to defaults!</p></div>';
    }
    
    // Get current values
    $settings = kb_get_customization_settings();
    $kb_page_url = home_url('/knowledge-base/'); // Yahan apna KB page URL daalo
    ?>
    
    <style>
    .kb-customization-wrapper {
        display: flex;
        gap: 30px;
        margin-top: 20px;
    }
    .kb-customization-left {
        flex: 0 0 45%;
        max-width: 45%;
    }
    .kb-customization-right {
        flex: 1;
        position: sticky;
        top: 32px;
        height: calc(100vh - 100px);
    }
    .kb-preview-container {
        background: #f0f0f1;
        border: 2px solid #c3c4c7;
        border-radius: 8px;
        padding: 15px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .kb-preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid #c3c4c7;
    }
    .kb-preview-header h3 {
        margin: 0;
        font-size: 16px;
    }
    .kb-preview-refresh {
        background: #2271b1;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }
    .kb-preview-refresh:hover {
        background: #135e96;
    }
    .kb-preview-iframe {
        flex: 1;
        border: none;
        background: white;
        border-radius: 4px;
        width: 100%;
    }
    @media (max-width: 1400px) {
        .kb-customization-wrapper {
            flex-direction: column;
        }
        .kb-customization-left {
            flex: 1;
            max-width: 100%;
        }
        .kb-customization-right {
            position: relative;
            top: 0;
            height: 600px;
        }
    }
    </style>
    
    <div class="wrap">
        <h1> knowzard Customization</h1>
        <p style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">
            Customize the appearance of your knowzard to match your brand. Changes will appear in real-time preview.
        </p>
        
        <div class="kb-customization-wrapper">
            <!-- LEFT SIDE: Form Controls -->
            <div class="kb-customization-left">
                <form method="post" action="" id="kb-customization-form">
                    <?php wp_nonce_field('kb_customization_nonce'); ?>
                    
                    <div class="kb-customization-panel">
                        
                        <!-- COLORS SECTION -->
                        <div class="kb-section">
                            <h2> Colors</h2>
                            <div class="kb-form-row">
                                <div class="kb-form-group">
                                    <label>Primary Color</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_primary_color" 
                                               value="<?php echo esc_attr($settings['kb_primary_color']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#2563eb"
                                               data-css-var="--kb-primary">
                                    </div>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Secondary Color</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_secondary_color" 
                                               value="<?php echo esc_attr($settings['kb_secondary_color']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#10b981"
                                               data-css-var="--kb-secondary">
                                    </div>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Text Color</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_text_color" 
                                               value="<?php echo esc_attr($settings['kb_text_color']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#1f2937"
                                               data-css-var="--kb-text">
                                    </div>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Background Color</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_bg_color" 
                                               value="<?php echo esc_attr($settings['kb_bg_color']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#ffffff"
                                               data-css-var="--kb-bg">
                                    </div>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Card Background</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_card_bg" 
                                               value="<?php echo esc_attr($settings['kb_card_bg']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#f9fafb"
                                               data-css-var="--kb-card-bg">
                                    </div>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Border Color</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_border_color" 
                                               value="<?php echo esc_attr($settings['kb_border_color']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#e5e7eb"
                                               data-css-var="--kb-border">
                                    </div>
                                </div>
                            </div>
                            
                            <h3 style="margin-top: 20px; font-size: 16px;">Gradient Colors (Header)</h3>
                            <div class="kb-form-row">
                                <div class="kb-form-group">
                                    <label>Gradient Start</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_gradient_start" 
                                               value="<?php echo esc_attr($settings['kb_gradient_start']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#2563eb"
                                               data-css-var="--kb-gradient-start">
                                    </div>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Gradient End</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_gradient_end" 
                                               value="<?php echo esc_attr($settings['kb_gradient_end']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#7c3aed"
                                               data-css-var="--kb-gradient-end">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- LOGO SECTION -->
                        <div class="kb-section">
                            <h2>🖼️ Logo</h2>
                            <div class="kb-form-row">
                                <div class="kb-form-group">
                                    <label>Logo URL</label>
                                    <div style="display: flex; gap: 10px;">
                                        <input type="text" 
                                               name="kb_logo_url" 
                                               id="kb_logo_url" 
                                               value="<?php echo esc_attr($settings['kb_logo_url']); ?>" 
                                               class="kb-preview-trigger"
                                               placeholder="https://example.com/logo.png">
                                        <button type="button" class="kb-upload-btn" id="kb_logo_upload">
                                            Upload Logo
                                        </button>
                                    </div>
                                    <?php if (!empty($settings['kb_logo_url'])): ?>
                                        <div class="kb-logo-preview">
                                            <img src="<?php echo esc_url($settings['kb_logo_url']); ?>" alt="Logo Preview">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Logo Height (px)</label>
                                    <input type="number" 
                                           name="kb_logo_height" 
                                           value="<?php echo esc_attr($settings['kb_logo_height']); ?>" 
                                           class="kb-preview-trigger"
                                           min="20" 
                                           max="200">
                                </div>
                            </div>
                        </div>
                        
                        <!-- TYPOGRAPHY SECTION -->
                        <div class="kb-section">
                            <h2>🔤 Typography</h2>
                            <div class="kb-form-row">
                                <div class="kb-form-group">
                                    <label>Body Font Family</label>
                                    <select name="kb_font_family" class="kb-preview-trigger">
                                        <option value="system-ui, -apple-system, sans-serif" <?php selected($settings['kb_font_family'], 'system-ui, -apple-system, sans-serif'); ?>>System Default</option>
                                        <option value="'Inter', sans-serif" <?php selected($settings['kb_font_family'], "'Inter', sans-serif"); ?>>Inter</option>
                                        <option value="'Roboto', sans-serif" <?php selected($settings['kb_font_family'], "'Roboto', sans-serif"); ?>>Roboto</option>
                                        <option value="'Open Sans', sans-serif" <?php selected($settings['kb_font_family'], "'Open Sans', sans-serif"); ?>>Open Sans</option>
                                        <option value="'Lato', sans-serif" <?php selected($settings['kb_font_family'], "'Lato', sans-serif"); ?>>Lato</option>
                                        <option value="'Poppins', sans-serif" <?php selected($settings['kb_font_family'], "'Poppins', sans-serif"); ?>>Poppins</option>
                                    </select>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Heading Font Family</label>
                                    <select name="kb_heading_font" class="kb-preview-trigger">
                                        <option value="inherit" <?php selected($settings['kb_heading_font'], 'inherit'); ?>>Same as Body</option>
                                        <option value="'Inter', sans-serif" <?php selected($settings['kb_heading_font'], "'Inter', sans-serif"); ?>>Inter</option>
                                        <option value="'Roboto', sans-serif" <?php selected($settings['kb_heading_font'], "'Roboto', sans-serif"); ?>>Roboto</option>
                                        <option value="'Montserrat', sans-serif" <?php selected($settings['kb_heading_font'], "'Montserrat', sans-serif"); ?>>Montserrat</option>
                                        <option value="'Playfair Display', serif" <?php selected($settings['kb_heading_font'], "'Playfair Display', serif"); ?>>Playfair Display</option>
                                    </select>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Base Font Size (px)</label>
                                    <input type="number" 
                                           name="kb_font_size" 
                                           value="<?php echo esc_attr($settings['kb_font_size']); ?>" 
                                           class="kb-preview-trigger"
                                           min="12" 
                                           max="20">
                                </div>
                            </div>
                        </div>
                        
                        <!-- HEADER SECTION -->
                        <div class="kb-section">
                            <h2>📋 Header</h2>
                            <div class="kb-form-row">
                                <div class="kb-form-group">
                                    <label>Header Background</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_header_bg" 
                                               value="<?php echo esc_attr($settings['kb_header_bg']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#ffffff"
                                               data-css-var="--kb-header-bg">
                                    </div>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Header Text Color</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_header_text" 
                                               value="<?php echo esc_attr($settings['kb_header_text']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#1f2937"
                                               data-css-var="--kb-header-text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- BUTTONS SECTION -->
                        <div class="kb-section">
                            <h2>🔘 Buttons</h2>
                            <div class="kb-form-row">
                                <div class="kb-form-group">
                                    <label>Border Radius (px)</label>
                                    <input type="number" 
                                           name="kb_button_radius" 
                                           value="<?php echo esc_attr($settings['kb_button_radius']); ?>" 
                                           class="kb-preview-trigger"
                                           min="0" 
                                           max="50">
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Hover Color</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_button_hover" 
                                               value="<?php echo esc_attr($settings['kb_button_hover']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#1d4ed8"
                                               data-css-var="--kb-button-hover">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CARDS SECTION -->
                        <div class="kb-section">
                            <h2>🃏 Cards</h2>
                            <div class="kb-form-row">
                                <div class="kb-form-group">
                                    <label>Border Radius (px)</label>
                                    <input type="number" 
                                           name="kb_card_radius" 
                                           value="<?php echo esc_attr($settings['kb_card_radius']); ?>" 
                                           class="kb-preview-trigger"
                                           min="0" 
                                           max="50">
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Shadow Style</label>
                                    <select name="kb_card_shadow" class="kb-preview-trigger">
                                        <option value="none" <?php selected($settings['kb_card_shadow'], 'none'); ?>>No Shadow</option>
                                        <option value="0 1px 3px rgba(0,0,0,0.1)" <?php selected($settings['kb_card_shadow'], '0 1px 3px rgba(0,0,0,0.1)'); ?>>Light</option>
                                        <option value="0 4px 6px rgba(0,0,0,0.1)" <?php selected($settings['kb_card_shadow'], '0 4px 6px rgba(0,0,0,0.1)'); ?>>Medium</option>
                                        <option value="0 10px 15px rgba(0,0,0,0.1)" <?php selected($settings['kb_card_shadow'], '0 10px 15px rgba(0,0,0,0.1)'); ?>>Heavy</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- HOME PAGE -->
                        <div class="kb-section">
                            <h2>✏️ Content Texts</h2>
                            <div class="kb-form-row">
                                <div class="kb-form-group">
                                    <label>Header Title</label>
                                    <input type="text" 
                                        name="kb_header_title" 
                                        value="<?php echo esc_attr($settings['kb_header_title']); ?>" 
                                        class="kb-preview-trigger"
                                        placeholder="knowzard">
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Header Description</label>
                                    <input type="text" 
                                        name="kb_header_description" 
                                        value="<?php echo esc_attr($settings['kb_header_description']); ?>" 
                                        class="kb-preview-trigger"
                                        placeholder="Find answers, guides, and documentation">
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Search Placeholder</label>
                                    <input type="text" 
                                        name="kb_search_placeholder" 
                                        value="<?php echo esc_attr($settings['kb_search_placeholder']); ?>" 
                                        class="kb-preview-trigger"
                                        placeholder="Search for articles, guides, tutorials...">
                                </div>
                                
                                <!-- YE NAYA FIELD ADD KIYA HAI -->
                                <div class="kb-form-group">
                                    <label>Breadcrumb Text</label>
                                    <input type="text" 
                                        name="kb_breadcrumb_text" 
                                        value="<?php echo esc_attr($settings['kb_breadcrumb_text']); ?>" 
                                        class="kb-preview-trigger"
                                        placeholder="knowzard">
                                    <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                                        💡 Yeh text breadcrumb ke last part mein show hoga (e.g., Home > knowzard).
                                    </p>
                                </div>
                                <!-- NAYA FIELD KHATAM -->
                            </div>
                        </div>
                        
                        <!-- FOOTER SECTION -->
                        <div class="kb-section">
                            <h2>🦶 Footer</h2>
                            <div class="kb-form-row">
                                <div class="kb-form-group">
                                    <label>Footer Background</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_footer_bg" 
                                               value="<?php echo esc_attr($settings['kb_footer_bg']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#1f2937"
                                               data-css-var="--kb-footer-bg">
                                    </div>
                                </div>
                                
                                <div class="kb-form-group">
                                    <label>Footer Text Color</label>
                                    <div class="kb-color-picker">
                                        <input type="text" 
                                               name="kb_footer_text" 
                                               value="<?php echo esc_attr($settings['kb_footer_text']); ?>" 
                                               class="kb-color-field kb-preview-trigger"
                                               data-default-color="#ffffff"
                                               data-css-var="--kb-footer-text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SUBMIT BUTTONS -->
                        <div class="kb-section">
                            <div class="kb-submit-row">
                                <button type="submit" name="kb_save_customization" class="button button-primary button-large">
                                    💾 Save Customization
                                </button>
                                <button type="submit" name="kb_reset_customization" class="button button-large" onclick="return confirm('Are you sure you want to reset all settings to defaults?')">
                                    🔄 Reset to Defaults
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </form>
            </div>
            
            <!-- RIGHT SIDE: Live Preview -->
            <div class="kb-customization-right">
                <div class="kb-preview-container">
                    <div class="kb-preview-header">
                        <h3>📱 Live Preview</h3>
                        <button type="button" class="kb-preview-refresh" onclick="document.getElementById('kb-preview-frame').src = document.getElementById('kb-preview-frame').src">
                            🔄 Refresh
                        </button>
                    </div>
                    <iframe id="kb-preview-frame" class="kb-preview-iframe" src="<?php echo esc_url($kb_page_url); ?>"></iframe>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Initialize color pickers
        $('.kb-color-field').wpColorPicker({
            change: function(event, ui) {
                updatePreview();
            }
        });
        
        // Live preview update
        let previewTimeout;
        $('.kb-preview-trigger').on('change input', function() {
            clearTimeout(previewTimeout);
            previewTimeout = setTimeout(updatePreview, 500);
        });
        
        function updatePreview() {
        const iframe = document.getElementById('kb-preview-frame');
        if (!iframe || !iframe.contentWindow) return;
        
        try {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            // TEXT UPDATES (Content changes)
            // Header title
            const headerTitle = iframeDoc.querySelector('.kb-header h1');
            if (headerTitle) headerTitle.textContent = $('input[name="kb_header_title"]').val() || 'knowzard';
            
            // Header description
            const headerDesc = iframeDoc.querySelector('.kb-header p');
            if (headerDesc) headerDesc.textContent = $('input[name="kb_header_description"]').val() || 'Find answers, guides, and documentation';
            
            // Search placeholder
            const searchInput = iframeDoc.querySelector('.kb-search-input');
            if (searchInput) searchInput.setAttribute('placeholder', $('input[name="kb_search_placeholder"]').val() || 'Search for articles, guides, tutorials...');
            
            // Breadcrumb text (last span in breadcrumb)
            const breadcrumbText = iframeDoc.querySelector('.kb-breadcrumb span:last-child');
            if (breadcrumbText) breadcrumbText.textContent = $('input[name="kb_breadcrumb_text"]').val() || 'knowzard';
            
            // CSS INJECTION
            let styleElement = iframeDoc.getElementById('kb-preview-styles');
            if (!styleElement) {
                styleElement = iframeDoc.createElement('style');
                styleElement.id = 'kb-preview-styles';
                iframeDoc.head.appendChild(styleElement);
            }
            
            let css = ':root {';
            css += '--kb-primary: ' + $('input[name="kb_primary_color"]').val() + ';';
            css += '--kb-secondary: ' + $('input[name="kb_secondary_color"]').val() + ';';
            css += '--kb-text: ' + $('input[name="kb_text_color"]').val() + ';';
            css += '--kb-bg: ' + $('input[name="kb_bg_color"]').val() + ';';
            css += '--kb-card-bg: ' + $('input[name="kb_card_bg"]').val() + ';';
            css += '--kb-border: ' + $('input[name="kb_border_color"]').val() + ';';
            css += '--kb-gradient-start: ' + $('input[name="kb_gradient_start"]').val() + ';';
            css += '--kb-gradient-end: ' + $('input[name="kb_gradient_end"]').val() + ';';
            css += '--kb-header-bg: ' + $('input[name="kb_header_bg"]').val() + ';';
            css += '--kb-header-text: ' + $('input[name="kb_header_text"]').val() + ';';
            css += '--kb-button-hover: ' + $('input[name="kb_button_hover"]').val() + ';';
            css += '--kb-footer-bg: ' + $('input[name="kb_footer_bg"]').val() + ';';
            css += '--kb-footer-text: ' + $('input[name="kb_footer_text"]').val() + ';';
            css += '}';
            
            css += 'body { font-family: ' + $('select[name="kb_font_family"]').val() + ' !important; ';
            css += 'font-size: ' + $('input[name="kb_font_size"]').val() + 'px !important; }';
            
            css += 'h1, h2, h3, h4, h5, h6 { font-family: ' + $('select[name="kb_heading_font"]').val() + ' !important; }';
            
            css += '.kb-button { border-radius: ' + $('input[name="kb_button_radius"]').val() + 'px !important; }';
            css += '.kb-card { border-radius: ' + $('input[name="kb_card_radius"]').val() + 'px !important; ';
            css += 'box-shadow: ' + $('select[name="kb_card_shadow"]').val() + ' !important; }';
            
            // Logo height fix - assuming selector in iframe is 'nav img' or '.custom-logo'; adjust if needed
            if ($('input[name="kb_logo_url"]').val()) {
                css += 'nav img, .custom-logo { height: ' + $('input[name="kb_logo_height"]').val() + 'px !important; max-height: ' + $('input[name="kb_logo_height"]').val() + 'px !important; }';
            }
            
            // Additional: Apply header bg to .kb-header if needed (for preview realism)
            // css += '.kb-header { background: var(--kb-header-bg) !important; color: var(--kb-header-text) !important; }';
            
            styleElement.textContent = css;
            
        } catch (e) {
            console.log('Preview update error:', e);
        }
    }
        
        // Logo upload
        $('#kb_logo_upload').on('click', function(e) {
            e.preventDefault();
            
            var mediaUploader = wp.media({
                title: 'Select Logo',
                button: {
                    text: 'Use this logo'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#kb_logo_url').val(attachment.url);
                
                // Show preview
                if ($('.kb-logo-preview').length === 0) {
                    $('#kb_logo_url').parent().after('<div class="kb-logo-preview"><img src="' + attachment.url + '" alt="Logo Preview"></div>');
                } else {
                    $('.kb-logo-preview img').attr('src', attachment.url);
                }
                
                updatePreview();
            });
            
            mediaUploader.open();
        });
        
        // Initial preview update after iframe loads
        $('#kb-preview-frame').on('load', function() {
            setTimeout(updatePreview, 1000);
        });
    });
    </script>
    <?php
}

/**
 * Get Customization Settings
 */
function kb_get_customization_settings() {
    $defaults = kb_get_default_settings();
    
    $settings = array();
    foreach ($defaults as $key => $default) {
        $settings[$key] = get_option($key, $default);
    }
    
    return $settings;
}

/**
 * Default Settings
 */
function kb_get_default_settings() {
    return array(
        'kb_primary_color' => '#2563eb',
        'kb_secondary_color' => '#10b981',
        'kb_text_color' => '#1f2937',
        'kb_bg_color' => '#ffffff',
        'kb_card_bg' => '#f9fafb',
        'kb_border_color' => '#e5e7eb',
        'kb_gradient_start' => '#2563eb',
        'kb_gradient_end' => '#7c3aed',
        'kb_logo_url' => '',
        'kb_logo_height' => 60,
        'kb_font_family' => 'system-ui, -apple-system, sans-serif',
        'kb_heading_font' => 'inherit',
        'kb_font_size' => 16,
        'kb_header_bg' => '#ffffff',
        'kb_header_text' => '#1f2937',
        'kb_button_radius' => 6,
        'kb_button_hover' => '#1d4ed8',
        'kb_card_radius' => 10,
        'kb_card_shadow' => '0 4px 6px rgba(0,0,0,0.1)',
        'kb_footer_bg' => '#1f2937',
        'kb_footer_text' => '#ffffff',
        'kb_header_title' => 'knowzard', 
        'kb_header_description' => 'Find answers, guides, and documentation',  
        'kb_search_placeholder' => 'Search for articles, guides, tutorials...',
        'kb_breadcrumb_text' => 'knowzard',
    );
}

/**
 * Apply Custom Styles to Frontend
 */
function kb_apply_custom_styles() {
    if (!is_plugin_activated5929()) {
        return;
    }
    
    $settings = kb_get_customization_settings();
    ?>
    <style>
        :root {
            --kb-primary-color: <?php echo esc_attr($settings['kb_primary_color']); ?>;
            --kb-secondary-color: <?php echo esc_attr($settings['kb_secondary_color']); ?>;
            --kb-text-color: <?php echo esc_attr($settings['kb_text_color']); ?>;
            --kb-bg-color: <?php echo esc_attr($settings['kb_bg_color']); ?>;
            --kb-card-bg: <?php echo esc_attr($settings['kb_card_bg']); ?>;
            --kb-border-color: <?php echo esc_attr($settings['kb_border_color']); ?>;
            --kb-gradient-start: <?php echo esc_attr($settings['kb_gradient_start']); ?>;
            --kb-gradient-end: <?php echo esc_attr($settings['kb_gradient_end']); ?>;
            --kb-button-radius: <?php echo esc_attr($settings['kb_button_radius']); ?>px;
            --kb-button-hover: <?php echo esc_attr($settings['kb_button_hover']); ?>;
            --kb-card-radius: <?php echo esc_attr($settings['kb_card_radius']); ?>px;
            --kb-card-shadow: <?php echo esc_attr($settings['kb_card_shadow']); ?>;
        }
        
        body {
            font-family: <?php echo esc_attr($settings['kb_font_family']); ?>;
            font-size: <?php echo esc_attr($settings['kb_font_size']); ?>px;
            color: var(--kb-text-color);
            background: var(--kb-bg-color);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: <?php echo esc_attr($settings['kb_heading_font']); ?>;
        }
        
        /* Header Styles */
        nav {
            background: <?php echo esc_attr($settings['kb_header_bg']); ?> !important;
            color: <?php echo esc_attr($settings['kb_header_text']); ?> !important;
        }
        
        nav a {
            color: <?php echo esc_attr($settings['kb_header_text']); ?> !important;
        }
        
        /* Logo Styles */
        .custom-logo,
        nav img {
            max-height: <?php echo esc_attr($settings['kb_logo_height']); ?>px !important;
        }
        
        /* Button Styles */
        .kb-search-submit,
        button[type="submit"],
        .button-primary,
        a[style*="background: var(--kb-primary-color)"] {
            border-radius: var(--kb-button-radius) !important;
        }
        
        .kb-search-submit:hover,
        button[type="submit"]:hover,
        .button-primary:hover {
            background: var(--kb-button-hover) !important;
        }
        
        /* Card Styles */
        .kb-card {
            background: var(--kb-card-bg) !important;
            border-radius: var(--kb-card-radius) !important;
            box-shadow: var(--kb-card-shadow) !important;
        }
        
        /* Footer Styles */
        footer {
            background: <?php echo esc_attr($settings['kb_footer_bg']); ?> !important;
            color: <?php echo esc_attr($settings['kb_footer_text']); ?> !important;
        }
        
        footer a {
            color: <?php echo esc_attr($settings['kb_footer_text']); ?> !important;
            opacity: 0.7;
        }
        
        footer a:hover {
            opacity: 1;
        }
    </style>
    
    <?php
    // Load Google Fonts if needed
    $fonts_to_load = array();
    
    if (strpos($settings['kb_font_family'], 'Inter') !== false) {
        $fonts_to_load[] = 'Inter:wght@300;400;500;600;700';
    }
    if (strpos($settings['kb_font_family'], 'Roboto') !== false || strpos($settings['kb_heading_font'], 'Roboto') !== false) {
        $fonts_to_load[] = 'Roboto:wght@300;400;500;700';
    }
    if (strpos($settings['kb_font_family'], 'Open Sans') !== false) {
        $fonts_to_load[] = 'Open+Sans:wght@300;400;600;700';
    }
    if (strpos($settings['kb_font_family'], 'Lato') !== false) {
        $fonts_to_load[] = 'Lato:wght@300;400;700';
    }
    if (strpos($settings['kb_font_family'], 'Poppins') !== false) {
        $fonts_to_load[] = 'Poppins:wght@300;400;500;600;700';
    }
    if (strpos($settings['kb_heading_font'], 'Montserrat') !== false) {
        $fonts_to_load[] = 'Montserrat:wght@400;600;700';
    }
    if (strpos($settings['kb_heading_font'], 'Playfair Display') !== false) {
        $fonts_to_load[] = 'Playfair+Display:wght@400;600;700';
    }
    
    if (!empty($fonts_to_load)) {
        $fonts_url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', array_unique($fonts_to_load)) . '&display=swap';
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        echo '<link href="' . esc_url($fonts_url) . '" rel="stylesheet">';
    }
}
add_action('wp_head', 'kb_apply_custom_styles');

/**
 * Visibility Control Page
 */
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
        <h1> Visibility Control</h1>
        <p style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">
            Control which posts and categories are visible in your knowzard.
        </p>
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_visibility_nonce'); ?>
            
            <div class="kb-admin-panel">
                <div class="kb-admin-section">
                    <h2>📄 Hide Posts</h2>
                    <p>Select posts you want to hide from the knowzard:</p>
                    
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
                    <p>Select categories you want to hide from the knowzard:</p>
                    
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
// AJAX FUNCTIONS
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
        get_template_directory_uri() . '/js/script.js',
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

function kb_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'kb_excerpt_more');
// Post Manager Page
function kb_post_manager_page() {
    if (!current_user_can('manage_options')) return;
    
    // Handle Create Post
    if (isset($_POST['kb_create_post'])) {
        check_admin_referer('kb_post_manager_nonce');
        
        $post_title = sanitize_text_field($_POST['kb_post_title']);
        $post_content = wp_kses_post($_POST['kb_post_content']);
        $parent_category = intval($_POST['kb_parent_category']);
        $subcategory_name = sanitize_text_field($_POST['kb_subcategory_name']);
        
        if (empty($post_title)) {
            echo '<div class="notice notice-error"><p>❌ Post title is required!</p></div>';
        } else {
            // Create sub-category if provided
            $sub_cat_id = 0;
            if (!empty($subcategory_name) && $parent_category > 0) {
                $sub_cat = wp_insert_term(
                    $subcategory_name,
                    'category',
                    array('parent' => $parent_category)
                );
                
                if (!is_wp_error($sub_cat)) {
                    $sub_cat_id = $sub_cat['term_id'];
                } else {
                    echo '<div class="notice notice-warning"><p>⚠️ Sub-category creation warning: ' . esc_html($sub_cat->get_error_message()) . '</p></div>';
                }
            }
            
            // Create post
            $categories = array();
            if ($parent_category > 0) {
                $categories[] = $parent_category;
            }
            if ($sub_cat_id > 0) {
                $categories[] = $sub_cat_id;
            }
            
            $post_data = array(
                'post_title' => $post_title,
                'post_content' => $post_content,
                'post_type' => 'post',
                'post_status' => 'publish',
            );
            
            $post_id = wp_insert_post($post_data);
            
            if (!is_wp_error($post_id) && !empty($categories)) {
                wp_set_post_categories($post_id, $categories);
            }
            
            if (!is_wp_error($post_id)) {
                echo '<div class="notice notice-success"><p>✅ Post created successfully! ID: ' . esc_html($post_id) . '</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>❌ Error creating post: ' . esc_html($post_id->get_error_message()) . '</p></div>';
            }
        }
    }
    
    // Handle Create Category
    if (isset($_POST['kb_create_category'])) {
        check_admin_referer('kb_post_manager_nonce');
        
        $category_name = sanitize_text_field($_POST['kb_category_name']);
        $category_desc = sanitize_text_field($_POST['kb_category_description']);
        
        if (empty($category_name)) {
            echo '<div class="notice notice-error"><p>❌ Category name is required!</p></div>';
        } else {
            $category = wp_insert_term(
                $category_name,
                'category',
                array('description' => $category_desc)
            );
            
            if (!is_wp_error($category)) {
                echo '<div class="notice notice-success"><p>✅ Category created successfully! ID: ' . esc_html($category['term_id']) . '</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>❌ Error: ' . esc_html($category->get_error_message()) . '</p></div>';
            }
        }
    }
    
    // Handle Delete Post
    if (isset($_POST['kb_delete_post'])) {
        check_admin_referer('kb_post_manager_nonce');
        
        $post_id = intval($_POST['kb_post_id_delete']);
        
        if (wp_delete_post($post_id, true)) {
            echo '<div class="notice notice-success"><p>✅ Post deleted successfully!</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>❌ Error deleting post!</p></div>';
        }
    }
    
    // Get all categories
    $all_categories = get_categories(array('hide_empty' => false));
    $all_posts = get_posts(array('numberposts' => -1, 'post_status' => 'publish'));
    
    ?>
    
    <style>
        .kb-manager-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }
        
        .kb-manager-panel {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .kb-manager-panel h2 {
            font-size: 20px;
            margin: 0 0 20px 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #2563eb;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .kb-form-group {
            margin-bottom: 16px;
        }
        
        .kb-form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
        }
        
        .kb-form-group input[type="text"],
        .kb-form-group input[type="number"],
        .kb-form-group select,
        .kb-form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        
        .kb-form-group input:focus,
        .kb-form-group select:focus,
        .kb-form-group textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .kb-form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .kb-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .kb-btn-primary {
            background: #2563eb;
            color: white;
        }
        
        .kb-btn-primary:hover {
            background: #1d4ed8;
        }
        
        .kb-btn-danger {
            background: #ef4444;
            color: white;
            font-size: 12px;
            padding: 6px 12px;
        }
        
        .kb-btn-danger:hover {
            background: #dc2626;
        }
        
        .kb-list-section {
            margin-top: 30px;
        }
        
        .kb-list-section h3 {
            font-size: 16px;
            margin: 0 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .kb-item-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        
        .kb-item {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }
        
        .kb-item:hover {
            background: #f9fafb;
        }
        
        .kb-item:last-child {
            border-bottom: none;
        }
        
        .kb-item-info {
            flex: 1;
        }
        
        .kb-item-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .kb-item-meta {
            font-size: 12px;
            color: #6b7280;
        }
        
        .kb-item-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 8px;
        }
        
        @media (max-width: 1200px) {
            .kb-manager-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <div class="wrap">
        <h1>knowzard Post & Category Manager</h1>
        <p style="font-size: 14px; color: #6b7280; margin-bottom: 30px;">
            Create and manage posts, categories, and sub-categories all in one place.
        </p>
        
        <div class="kb-manager-container">
            
            <!-- LEFT: Create Post & Category -->
            <div>
                <!-- CREATE CATEGORY -->
                <div class="kb-manager-panel">
                    <h2>📂 Create Category</h2>
                    <form method="post" action="">
                        <?php wp_nonce_field('kb_post_manager_nonce'); ?>
                        
                        <div class="kb-form-group">
                            <label>Category Name *</label>
                            <input type="text" name="kb_category_name" placeholder="e.g., Getting Started" required>
                        </div>
                        
                        <div class="kb-form-group">
                            <label>Description</label>
                            <textarea name="kb_category_description" placeholder="Optional category description..."></textarea>
                        </div>
                        
                        <button type="submit" name="kb_create_category" class="kb-btn kb-btn-primary">
                            ➕ Create Category
                        </button>
                    </form>
                </div>
                
                <!-- CREATE POST -->
                <div class="kb-manager-panel" style="margin-top: 30px;">
                    <h2>✍️ Create Post</h2>
                    <form method="post" action="">
                        <?php wp_nonce_field('kb_post_manager_nonce'); ?>
                        
                        <div class="kb-form-group">
                            <label>Post Title *</label>
                            <input type="text" name="kb_post_title" placeholder="Enter post title" required>
                        </div>
                        
                        <div class="kb-form-group">
                            <label>Parent Category</label>
                            <select name="kb_parent_category">
                                <option value="0">-- Select Category --</option>
                                <?php foreach ($all_categories as $cat): ?>
                                    <option value="<?php echo esc_attr($cat->term_id); ?>">
                                        <?php echo esc_html($cat->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="kb-form-group">
                            <label>Sub-Category (will be created automatically)</label>
                            <input type="text" name="kb_subcategory_name" placeholder="e.g., Installation (optional)">
                            <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                                💡 If you specify a sub-category, it will be created under the parent category.
                            </p>
                        </div>
                        
                        <div class="kb-form-group">
                            <label>Post Content</label>
                            <?php 
                            wp_editor(
                                '',
                                'kb_post_content',
                                array(
                                    'textarea_rows' => 10,
                                    'media_buttons' => true,
                                    'teeny' => false,
                                )
                            );
                            ?>
                        </div>
                        
                        <button type="submit" name="kb_create_post" class="kb-btn kb-btn-primary" style="margin-top: 15px;">
                            ✍️ Create Post
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- RIGHT: View & Delete -->
            <div>
                <!-- CATEGORIES LIST -->
                <div class="kb-manager-panel">
                    <h2>📂 All Categories (<?php echo count($all_categories); ?>)</h2>
                    
                    <?php if (empty($all_categories)): ?>
                        <p style="color: #6b7280; font-style: italic;">No categories yet. Create one above!</p>
                    <?php else: ?>
                        <div class="kb-item-list">
                            <?php foreach ($all_categories as $cat): ?>
                                <div class="kb-item">
                                    <div class="kb-item-info">
                                        <div class="kb-item-title"><?php echo esc_html($cat->name); ?></div>
                                        <div class="kb-item-meta">
                                            <span class="kb-item-badge"><?php echo esc_html($cat->count); ?> posts</span>
                                            ID: <?php echo esc_html($cat->term_id); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- POSTS LIST -->
                <div class="kb-manager-panel" style="margin-top: 30px;">
                    <h2>📝 All Posts (<?php echo count($all_posts); ?>)</h2>
                    
                    <?php if (empty($all_posts)): ?>
                        <p style="color: #6b7280; font-style: italic;">No posts yet. Create one above!</p>
                    <?php else: ?>
                        <div class="kb-item-list">
                            <?php foreach ($all_posts as $post): ?>
                                <div class="kb-item">
                                    <div class="kb-item-info">
                                        <div class="kb-item-title"><?php echo esc_html($post->post_title); ?></div>
                                        <div class="kb-item-meta">
                                            <?php 
                                            $cats = get_the_category($post->ID);
                                            if (!empty($cats)) {
                                                foreach ($cats as $cat) {
                                                    echo '<span class="kb-item-badge">' . esc_html($cat->name) . '</span>';
                                                }
                                            }
                                            ?>
                                            <br>ID: <?php echo esc_html($post->ID); ?>
                                        </div>
                                    </div>
                                    <form method="post" action="" style="display: inline;">
                                        <?php wp_nonce_field('kb_post_manager_nonce'); ?>
                                        <input type="hidden" name="kb_post_id_delete" value="<?php echo esc_attr($post->ID); ?>">
                                        <button type="submit" name="kb_delete_post" class="kb-btn kb-btn-danger" onclick="return confirm('Are you sure?');">
                                            🗑️ Delete
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php
}