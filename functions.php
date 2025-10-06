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
    
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'knowledge-base'),
    ));
}
add_action('after_setup_theme', 'kb_theme_setup');

// Enqueue Styles and Scripts (Only if licensed)
function kb_theme_scripts() {
    if (!is_plugin_activated5929()) {
        return;
    }
    
    wp_enqueue_style('kb-style', get_stylesheet_uri());
    
    // Enhanced Scripts
    wp_enqueue_script('kb-search', get_template_directory_uri() . '/js/enhanced-search.js', array('jquery'), '1.0', true);
    wp_enqueue_script('kb-loading', get_template_directory_uri() . '/js/loading-states.js', array('jquery'), '1.0', true);
    wp_enqueue_script('kb-accessibility', get_template_directory_uri() . '/js/accessibility.js', array('jquery'), '1.0', true);
    wp_enqueue_script('kb-single', get_template_directory_uri() . '/js/single-post.js', array('jquery'), '1.0', true);
    
    wp_enqueue_media();
    
    wp_localize_script('kb-search', 'kbAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('kb_nonce')
    ));
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
 * Knowledge Base Theme Functions - Complete Customization
 */

// Admin Menu
function kb_add_admin_menu() {
    add_menu_page(
        'Knowledge Base Settings',
        'KB Settings',
        'manage_options',
        'kb-settings',
        'kb_settings_page',
        'dashicons-book',
        30
    );
    
    add_submenu_page(
        'kb-settings',
        'General Settings',
        'General',
        'manage_options',
        'kb-settings'
    );
    
    add_submenu_page(
        'kb-settings',
        'Colors & Design',
        'Colors & Design',
        'manage_options',
        'kb-colors',
        'kb_colors_page'
    );
    
    add_submenu_page(
        'kb-settings',
        'Typography',
        'Typography',
        'manage_options',
        'kb-typography',
        'kb_typography_page'
    );
    
    add_submenu_page(
        'kb-settings',
        'Layout & Spacing',
        'Layout & Spacing',
        'manage_options',
        'kb-layout',
        'kb_layout_page'
    );
    
    add_submenu_page(
        'kb-settings',
        'Header & Navigation',
        'Header & Nav',
        'manage_options',
        'kb-header',
        'kb_header_page'
    );
    
    add_submenu_page(
        'kb-settings',
        'Cards & Components',
        'Cards & Components',
        'manage_options',
        'kb-cards',
        'kb_cards_page'
    );
    
    add_submenu_page(
        'kb-settings',
        'Search & Breadcrumb',
        'Search & Breadcrumb',
        'manage_options',
        'kb-search',
        'kb_search_page'
    );
    
    add_submenu_page(
        'kb-settings',
        'Footer Settings',
        'Footer',
        'manage_options',
        'kb-footer',
        'kb_footer_page'
    );
    
    add_submenu_page(
        'kb-settings',
        'Visibility Control',
        'Visibility Control',
        'manage_options',
        'kb-visibility',
        'kb_visibility_page'
    );
    
    add_submenu_page(
        'kb-settings',
        'Import/Export',
        'Import/Export',
        'manage_options',
        'kb-import-export',
        'kb_import_export_page'
    );
}
add_action('admin_menu', 'kb_add_admin_menu');

// General Settings Page
function kb_settings_page() {
    if (!current_user_can('manage_options')) return;
    
    if (isset($_POST['kb_save_general'])) {
        check_admin_referer('kb_general_nonce');
        
        update_option('kb_site_title', sanitize_text_field($_POST['kb_site_title']));
        update_option('kb_site_tagline', sanitize_text_field($_POST['kb_site_tagline']));
        update_option('kb_logo_url', esc_url_raw($_POST['kb_logo_url']));
        update_option('kb_favicon_url', esc_url_raw($_POST['kb_favicon_url']));
        update_option('kb_show_breadcrumb', isset($_POST['kb_show_breadcrumb']));
        update_option('kb_show_search', isset($_POST['kb_show_search']));
        update_option('kb_show_categories', isset($_POST['kb_show_categories']));
        update_option('kb_posts_per_page', intval($_POST['kb_posts_per_page']));
        
        echo '<div class="notice notice-success"><p>✅ Settings saved successfully!</p></div>';
    }
    
    $site_title = get_option('kb_site_title', get_bloginfo('name'));
    $site_tagline = get_option('kb_site_tagline', get_bloginfo('description'));
    $logo_url = get_option('kb_logo_url', '');
    $favicon_url = get_option('kb_favicon_url', '');
    $show_breadcrumb = get_option('kb_show_breadcrumb', true);
    $show_search = get_option('kb_show_search', true);
    $show_categories = get_option('kb_show_categories', true);
    $posts_per_page = get_option('kb_posts_per_page', 12);
    ?>
    
    <div class="wrap">
        <h1>⚙️ General Settings</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_general_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Site Title</th>
                    <td>
                        <input type="text" name="kb_site_title" value="<?php echo esc_attr($site_title); ?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Site Tagline</th>
                    <td>
                        <input type="text" name="kb_site_tagline" value="<?php echo esc_attr($site_tagline); ?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Logo URL</th>
                    <td>
                        <input type="text" name="kb_logo_url" id="kb_logo_url" value="<?php echo esc_attr($logo_url); ?>" class="regular-text">
                        <button type="button" class="button kb-upload-btn" data-target="kb_logo_url">Upload Logo</button>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Favicon URL</th>
                    <td>
                        <input type="text" name="kb_favicon_url" id="kb_favicon_url" value="<?php echo esc_attr($favicon_url); ?>" class="regular-text">
                        <button type="button" class="button kb-upload-btn" data-target="kb_favicon_url">Upload Favicon</button>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Display Options</th>
                    <td>
                        <label>
                            <input type="checkbox" name="kb_show_breadcrumb" <?php checked($show_breadcrumb); ?>>
                            Show Breadcrumb
                        </label><br>
                        <label>
                            <input type="checkbox" name="kb_show_search" <?php checked($show_search); ?>>
                            Show Search Bar
                        </label><br>
                        <label>
                            <input type="checkbox" name="kb_show_categories" <?php checked($show_categories); ?>>
                            Show Category Tabs
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Posts Per Page</th>
                    <td>
                        <input type="number" name="kb_posts_per_page" value="<?php echo esc_attr($posts_per_page); ?>" min="1" max="100">
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" name="kb_save_general" class="button button-primary">💾 Save Settings</button>
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.kb-upload-btn').click(function(e) {
            e.preventDefault();
            var targetInput = $(this).data('target');
            var image = wp.media({
                title: 'Upload Image',
                multiple: false
            }).open().on('select', function() {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#' + targetInput).val(image_url);
            });
        });
    });
    </script>
    <?php
}

// Colors & Design Page
function kb_colors_page() {
    if (!current_user_can('manage_options')) return;
    
    if (isset($_POST['kb_save_colors'])) {
        check_admin_referer('kb_colors_nonce');
        
        update_option('kb_primary_color', sanitize_hex_color($_POST['kb_primary_color']));
        update_option('kb_secondary_color', sanitize_hex_color($_POST['kb_secondary_color']));
        update_option('kb_text_color', sanitize_hex_color($_POST['kb_text_color']));
        update_option('kb_bg_color', sanitize_hex_color($_POST['kb_bg_color']));
        update_option('kb_card_bg', sanitize_hex_color($_POST['kb_card_bg']));
        update_option('kb_border_color', sanitize_hex_color($_POST['kb_border_color']));
        update_option('kb_gradient_start', sanitize_hex_color($_POST['kb_gradient_start']));
        update_option('kb_gradient_end', sanitize_hex_color($_POST['kb_gradient_end']));
        update_option('kb_gradient_direction', sanitize_text_field($_POST['kb_gradient_direction']));
        update_option('kb_link_color', sanitize_hex_color($_POST['kb_link_color']));
        update_option('kb_link_hover_color', sanitize_hex_color($_POST['kb_link_hover_color']));
        update_option('kb_bg_image', esc_url_raw($_POST['kb_bg_image']));
        update_option('kb_bg_opacity', floatval($_POST['kb_bg_opacity']));
        update_option('kb_bg_attachment', sanitize_text_field($_POST['kb_bg_attachment']));
        update_option('kb_bg_size', sanitize_text_field($_POST['kb_bg_size']));
        
        echo '<div class="notice notice-success"><p>✅ Colors saved!</p></div>';
    }
    
    $defaults = array(
        'kb_primary_color' => '#2563eb',
        'kb_secondary_color' => '#10b981',
        'kb_text_color' => '#1f2937',
        'kb_bg_color' => '#ffffff',
        'kb_card_bg' => '#f9fafb',
        'kb_border_color' => '#e5e7eb',
        'kb_gradient_start' => '#2563eb',
        'kb_gradient_end' => '#7c3aed',
        'kb_gradient_direction' => '135deg',
        'kb_link_color' => '#2563eb',
        'kb_link_hover_color' => '#1d4ed8',
        'kb_bg_image' => '',
        'kb_bg_opacity' => 1,
        'kb_bg_attachment' => 'scroll',
        'kb_bg_size' => 'cover'
    );
    
    foreach ($defaults as $key => $default) {
        $$key = get_option($key, $default);
    }
    ?>
    
    <div class="wrap">
        <h1>🎨 Colors & Design</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_colors_nonce'); ?>
            
            <h2>Primary Colors</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Primary Color</th>
                    <td>
                        <input type="text" name="kb_primary_color" value="<?php echo esc_attr($kb_primary_color); ?>" class="color-picker" data-default-color="#2563eb">
                        <p class="description">Main theme color (buttons, links, etc.)</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Secondary Color</th>
                    <td>
                        <input type="text" name="kb_secondary_color" value="<?php echo esc_attr($kb_secondary_color); ?>" class="color-picker" data-default-color="#10b981">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Text Color</th>
                    <td>
                        <input type="text" name="kb_text_color" value="<?php echo esc_attr($kb_text_color); ?>" class="color-picker" data-default-color="#1f2937">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Background Color</th>
                    <td>
                        <input type="text" name="kb_bg_color" value="<?php echo esc_attr($kb_bg_color); ?>" class="color-picker" data-default-color="#ffffff">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Card Background</th>
                    <td>
                        <input type="text" name="kb_card_bg" value="<?php echo esc_attr($kb_card_bg); ?>" class="color-picker" data-default-color="#f9fafb">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Border Color</th>
                    <td>
                        <input type="text" name="kb_border_color" value="<?php echo esc_attr($kb_border_color); ?>" class="color-picker" data-default-color="#e5e7eb">
                    </td>
                </tr>
            </table>
            
            <h2>Gradient Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Gradient Start</th>
                    <td>
                        <input type="text" name="kb_gradient_start" value="<?php echo esc_attr($kb_gradient_start); ?>" class="color-picker" data-default-color="#2563eb">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Gradient End</th>
                    <td>
                        <input type="text" name="kb_gradient_end" value="<?php echo esc_attr($kb_gradient_end); ?>" class="color-picker" data-default-color="#7c3aed">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Gradient Direction</th>
                    <td>
                        <input type="text" name="kb_gradient_direction" value="<?php echo esc_attr($kb_gradient_direction); ?>" placeholder="135deg">
                        <p class="description">e.g., 135deg, to right, to bottom</p>
                    </td>
                </tr>
            </table>
            
            <h2>Link Colors</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Link Color</th>
                    <td>
                        <input type="text" name="kb_link_color" value="<?php echo esc_attr($kb_link_color); ?>" class="color-picker" data-default-color="#2563eb">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Link Hover Color</th>
                    <td>
                        <input type="text" name="kb_link_hover_color" value="<?php echo esc_attr($kb_link_hover_color); ?>" class="color-picker" data-default-color="#1d4ed8">
                    </td>
                </tr>
            </table>
            
            <h2>Background Image</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Background Image URL</th>
                    <td>
                        <input type="text" name="kb_bg_image" id="kb_bg_image" value="<?php echo esc_attr($kb_bg_image); ?>" class="regular-text">
                        <button type="button" class="button kb-upload-btn" data-target="kb_bg_image">Upload Image</button>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Background Opacity</th>
                    <td>
                        <input type="range" name="kb_bg_opacity" value="<?php echo esc_attr($kb_bg_opacity); ?>" min="0" max="1" step="0.1">
                        <span id="opacity-value"><?php echo esc_html($kb_bg_opacity); ?></span>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Background Attachment</th>
                    <td>
                        <select name="kb_bg_attachment">
                            <option value="scroll" <?php selected($kb_bg_attachment, 'scroll'); ?>>Scroll</option>
                            <option value="fixed" <?php selected($kb_bg_attachment, 'fixed'); ?>>Fixed</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Background Size</th>
                    <td>
                        <select name="kb_bg_size">
                            <option value="cover" <?php selected($kb_bg_size, 'cover'); ?>>Cover</option>
                            <option value="contain" <?php selected($kb_bg_size, 'contain'); ?>>Contain</option>
                            <option value="auto" <?php selected($kb_bg_size, 'auto'); ?>>Auto</option>
                        </select>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" name="kb_save_colors" class="button button-primary">💾 Save Colors</button>
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.color-picker').wpColorPicker();
        
        $('.kb-upload-btn').click(function(e) {
            e.preventDefault();
            var targetInput = $(this).data('target');
            var image = wp.media({
                title: 'Upload Image',
                multiple: false
            }).open().on('select', function() {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#' + targetInput).val(image_url);
            });
        });
        
        $('input[name="kb_bg_opacity"]').on('input', function() {
            $('#opacity-value').text($(this).val());
        });
    });
    </script>
    <?php
}

// Typography Page
function kb_typography_page() {
    if (!current_user_can('manage_options')) return;
    
    if (isset($_POST['kb_save_typography'])) {
        check_admin_referer('kb_typography_nonce');
        
        update_option('kb_font_family', sanitize_text_field($_POST['kb_font_family']));
        update_option('kb_custom_font_url', esc_url_raw($_POST['kb_custom_font_url']));
        update_option('kb_font_size_base', intval($_POST['kb_font_size_base']));
        update_option('kb_font_size_h1', intval($_POST['kb_font_size_h1']));
        update_option('kb_font_size_h2', intval($_POST['kb_font_size_h2']));
        update_option('kb_font_size_h3', intval($_POST['kb_font_size_h3']));
        update_option('kb_line_height', floatval($_POST['kb_line_height']));
        update_option('kb_font_weight_normal', intval($_POST['kb_font_weight_normal']));
        update_option('kb_font_weight_bold', intval($_POST['kb_font_weight_bold']));
        update_option('kb_letter_spacing', floatval($_POST['kb_letter_spacing']));
        
        echo '<div class="notice notice-success"><p>✅ Typography saved!</p></div>';
    }
    
    $font_family = get_option('kb_font_family', 'System Default');
    $custom_font_url = get_option('kb_custom_font_url', '');
    $font_size_base = get_option('kb_font_size_base', 16);
    $font_size_h1 = get_option('kb_font_size_h1', 36);
    $font_size_h2 = get_option('kb_font_size_h2', 28);
    $font_size_h3 = get_option('kb_font_size_h3', 22);
    $line_height = get_option('kb_line_height', 1.6);
    $font_weight_normal = get_option('kb_font_weight_normal', 400);
    $font_weight_bold = get_option('kb_font_weight_bold', 700);
    $letter_spacing = get_option('kb_letter_spacing', 0);
    ?>
    
    <div class="wrap">
        <h1>✍️ Typography Settings</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_typography_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Font Family</th>
                    <td>
                        <select name="kb_font_family">
                            <option value="System Default" <?php selected($font_family, 'System Default'); ?>>System Default</option>
                            <option value="Arial, sans-serif" <?php selected($font_family, 'Arial, sans-serif'); ?>>Arial</option>
                            <option value="Georgia, serif" <?php selected($font_family, 'Georgia, serif'); ?>>Georgia</option>
                            <option value="'Courier New', monospace" <?php selected($font_family, "'Courier New', monospace"); ?>>Courier New</option>
                            <option value="'Times New Roman', serif" <?php selected($font_family, "'Times New Roman', serif"); ?>>Times New Roman</option>
                            <option value="'Roboto', sans-serif" <?php selected($font_family, "'Roboto', sans-serif"); ?>>Roboto (Google Font)</option>
                            <option value="'Open Sans', sans-serif" <?php selected($font_family, "'Open Sans', sans-serif"); ?>>Open Sans (Google Font)</option>
                            <option value="'Lato', sans-serif" <?php selected($font_family, "'Lato', sans-serif"); ?>>Lato (Google Font)</option>
                            <option value="'Montserrat', sans-serif" <?php selected($font_family, "'Montserrat', sans-serif"); ?>>Montserrat (Google Font)</option>
                            <option value="'Poppins', sans-serif" <?php selected($font_family, "'Poppins', sans-serif"); ?>>Poppins (Google Font)</option>
                            <option value="Custom" <?php selected($font_family, 'Custom'); ?>>Custom Font URL</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Custom Font URL</th>
                    <td>
                        <input type="text" name="kb_custom_font_url" value="<?php echo esc_attr($custom_font_url); ?>" class="regular-text" placeholder="https://fonts.googleapis.com/css2?family=...">
                        <p class="description">Google Fonts ya custom font URL (only if Custom selected above)</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Base Font Size (px)</th>
                    <td>
                        <input type="number" name="kb_font_size_base" value="<?php echo esc_attr($font_size_base); ?>" min="12" max="24">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">H1 Font Size (px)</th>
                    <td>
                        <input type="number" name="kb_font_size_h1" value="<?php echo esc_attr($font_size_h1); ?>" min="20" max="72">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">H2 Font Size (px)</th>
                    <td>
                        <input type="number" name="kb_font_size_h2" value="<?php echo esc_attr($font_size_h2); ?>" min="18" max="60">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">H3 Font Size (px)</th>
                    <td>
                        <input type="number" name="kb_font_size_h3" value="<?php echo esc_attr($font_size_h3); ?>" min="16" max="48">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Line Height</th>
                    <td>
                        <input type="number" name="kb_line_height" value="<?php echo esc_attr($line_height); ?>" min="1" max="3" step="0.1">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Normal Font Weight</th>
                    <td>
                        <select name="kb_font_weight_normal">
                            <option value="300" <?php selected($font_weight_normal, 300); ?>>Light (300)</option>
                            <option value="400" <?php selected($font_weight_normal, 400); ?>>Normal (400)</option>
                            <option value="500" <?php selected($font_weight_normal, 500); ?>>Medium (500)</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Bold Font Weight</th>
                    <td>
                        <select name="kb_font_weight_bold">
                            <option value="600" <?php selected($font_weight_bold, 600); ?>>Semi-Bold (600)</option>
                            <option value="700" <?php selected($font_weight_bold, 700); ?>>Bold (700)</option>
                            <option value="800" <?php selected($font_weight_bold, 800); ?>>Extra-Bold (800)</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Letter Spacing (px)</th>
                    <td>
                        <input type="number" name="kb_letter_spacing" value="<?php echo esc_attr($letter_spacing); ?>" min="-2" max="5" step="0.1">
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" name="kb_save_typography" class="button button-primary">💾 Save Typography</button>
            </p>
        </form>
    </div>
    <?php
}

// Layout & Spacing Page
function kb_layout_page() {
    if (!current_user_can('manage_options')) return;
    
    if (isset($_POST['kb_save_layout'])) {
        check_admin_referer('kb_layout_nonce');
        
        update_option('kb_container_width', intval($_POST['kb_container_width']));
        update_option('kb_section_padding_top', intval($_POST['kb_section_padding_top']));
        update_option('kb_section_padding_bottom', intval($_POST['kb_section_padding_bottom']));
        update_option('kb_card_gap', intval($_POST['kb_card_gap']));
        update_option('kb_card_padding', intval($_POST['kb_card_padding']));
        update_option('kb_card_border_radius', intval($_POST['kb_card_border_radius']));
        update_option('kb_card_border_width', intval($_POST['kb_card_border_width']));
        update_option('kb_button_padding_x', intval($_POST['kb_button_padding_x']));
        update_option('kb_button_padding_y', intval($_POST['kb_button_padding_y']));
        update_option('kb_button_border_radius', intval($_POST['kb_button_border_radius']));
        
        echo '<div class="notice notice-success"><p>✅ Layout saved!</p></div>';
    }
    
    $container_width = get_option('kb_container_width', 1200);
    $section_padding_top = get_option('kb_section_padding_top', 40);
    $section_padding_bottom = get_option('kb_section_padding_bottom', 40);
    $card_gap = get_option('kb_card_gap', 20);
    $card_padding = get_option('kb_card_padding', 24);
    $card_border_radius = get_option('kb_card_border_radius', 12);
    $card_border_width = get_option('kb_card_border_width', 1);
    $button_padding_x = get_option('kb_button_padding_x', 20);
    $button_padding_y = get_option('kb_button_padding_y', 10);
    $button_border_radius = get_option('kb_button_border_radius', 6);
    ?>
    
    <div class="wrap">
        <h1>📐 Layout & Spacing</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_layout_nonce'); ?>
            
            <h2>Container Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Container Max Width (px)</th>
                    <td>
                        <input type="number" name="kb_container_width" value="<?php echo esc_attr($container_width); ?>" min="960" max="1920">
                    </td>
                </tr>
            </table>
            
            <h2>Section Spacing</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Section Padding Top (px)</th>
                    <td>
                        <input type="number" name="kb_section_padding_top" value="<?php echo esc_attr($section_padding_top); ?>" min="0" max="200">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Section Padding Bottom (px)</th>
                    <td>
                        <input type="number" name="kb_section_padding_bottom" value="<?php echo esc_attr($section_padding_bottom); ?>" min="0" max="200">
                    </td>
                </tr>
            </table>
            
            <h2>Card Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Card Gap (px)</th>
                    <td>
                        <input type="number" name="kb_card_gap" value="<?php echo esc_attr($card_gap); ?>" min="0" max="100">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Card Padding (px)</th>
                    <td>
                        <input type="number" name="kb_card_padding" value="<?php echo esc_attr($card_padding); ?>" min="0" max="100">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Card Border Radius (px)</th>
                    <td>
                        <input type="number" name="kb_card_border_radius" value="<?php echo esc_attr($card_border_radius); ?>" min="0" max="50">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Card Border Width (px)</th>
                    <td>
                        <input type="number" name="kb_card_border_width" value="<?php echo esc_attr($card_border_width); ?>" min="0" max="10">
                    </td>
                </tr>
            </table>
            
            <h2>Button Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Button Padding X (px)</th>
                    <td>
                        <input type="number" name="kb_button_padding_x" value="<?php echo esc_attr($button_padding_x); ?>" min="0" max="100">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Button Padding Y (px)</th>
                    <td>
                        <input type="number" name="kb_button_padding_y" value="<?php echo esc_attr($button_padding_y); ?>" min="0" max="50">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Button Border Radius (px)</th>
                    <td>
                        <input type="number" name="kb_button_border_radius" value="<?php echo esc_attr($button_border_radius); ?>" min="0" max="50">
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" name="kb_save_layout" class="button button-primary">💾 Save Layout</button>
            </p>
        </form>
    </div>
    <?php
}

// Header & Navigation Page
function kb_header_page() {
    if (!current_user_can('manage_options')) return;
    
    if (isset($_POST['kb_save_header'])) {
        check_admin_referer('kb_header_nonce');
        
        update_option('kb_header_bg_color', sanitize_hex_color($_POST['kb_header_bg_color']));
        update_option('kb_header_text_color', sanitize_hex_color($_POST['kb_header_text_color']));
        update_option('kb_header_height', intval($_POST['kb_header_height']));
        update_option('kb_header_sticky', isset($_POST['kb_header_sticky']));
        update_option('kb_nav_font_size', intval($_POST['kb_nav_font_size']));
        update_option('kb_nav_gap', intval($_POST['kb_nav_gap']));
        
        echo '<div class="notice notice-success"><p>✅ Header saved!</p></div>';
    }
    
    $header_bg_color = get_option('kb_header_bg_color', '#ffffff');
    $header_text_color = get_option('kb_header_text_color', '#1f2937');
    $header_height = get_option('kb_header_height', 60);
    $header_sticky = get_option('kb_header_sticky', true);
    $nav_font_size = get_option('kb_nav_font_size', 14);
    $nav_gap = get_option('kb_nav_gap', 25);
    ?>
    
    <div class="wrap">
        <h1>🎯 Header & Navigation</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_header_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Header Background Color</th>
                    <td>
                        <input type="text" name="kb_header_bg_color" value="<?php echo esc_attr($header_bg_color); ?>" class="color-picker">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Header Text Color</th>
                    <td>
                        <input type="text" name="kb_header_text_color" value="<?php echo esc_attr($header_text_color); ?>" class="color-picker">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Header Height (px)</th>
                    <td>
                        <input type="number" name="kb_header_height" value="<?php echo esc_attr($header_height); ?>" min="40" max="150">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Sticky Header</th>
                    <td>
                        <label>
                            <input type="checkbox" name="kb_header_sticky" <?php checked($header_sticky); ?>>
                            Enable sticky header on scroll
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Navigation Font Size (px)</th>
                    <td>
                        <input type="number" name="kb_nav_font_size" value="<?php echo esc_attr($nav_font_size); ?>" min="10" max="24">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Navigation Item Gap (px)</th>
                    <td>
                        <input type="number" name="kb_nav_gap" value="<?php echo esc_attr($nav_gap); ?>" min="0" max="100">
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" name="kb_save_header" class="button button-primary">💾 Save Header</button>
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.color-picker').wpColorPicker();
    });
    </script>
    <?php
}

// Cards & Components Page
function kb_cards_page() {
    if (!current_user_can('manage_options')) return;
    
    if (isset($_POST['kb_save_cards'])) {
        check_admin_referer('kb_cards_nonce');
        
        update_option('kb_card_hover_effect', sanitize_text_field($_POST['kb_card_hover_effect']));
        update_option('kb_card_shadow', sanitize_text_field($_POST['kb_card_shadow']));
        update_option('kb_card_hover_shadow', sanitize_text_field($_POST['kb_card_hover_shadow']));
        update_option('kb_card_icon_size', intval($_POST['kb_card_icon_size']));
        update_option('kb_card_icon_bg', sanitize_hex_color($_POST['kb_card_icon_bg']));
        update_option('kb_card_icon_color', sanitize_hex_color($_POST['kb_card_icon_color']));
        update_option('kb_card_title_size', intval($_POST['kb_card_title_size']));
        update_option('kb_card_desc_size', intval($_POST['kb_card_desc_size']));
        update_option('kb_transition_speed', floatval($_POST['kb_transition_speed']));
        
        echo '<div class="notice notice-success"><p>✅ Card settings saved!</p></div>';
    }
    
    $card_hover_effect = get_option('kb_card_hover_effect', 'lift');
    $card_shadow = get_option('kb_card_shadow', '0 2px 4px rgba(0,0,0,0.1)');
    $card_hover_shadow = get_option('kb_card_hover_shadow', '0 10px 20px rgba(0,0,0,0.1)');
    $card_icon_size = get_option('kb_card_icon_size', 48);
    $card_icon_bg = get_option('kb_card_icon_bg', '#2563eb');
    $card_icon_color = get_option('kb_card_icon_color', '#ffffff');
    $card_title_size = get_option('kb_card_title_size', 20);
    $card_desc_size = get_option('kb_card_desc_size', 14);
    $transition_speed = get_option('kb_transition_speed', 0.3);
    ?>
    
    <div class="wrap">
        <h1>🎴 Cards & Components</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_cards_nonce'); ?>
            
            <h2>Card Hover Effects</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Hover Effect</th>
                    <td>
                        <select name="kb_card_hover_effect">
                            <option value="none" <?php selected($card_hover_effect, 'none'); ?>>None</option>
                            <option value="lift" <?php selected($card_hover_effect, 'lift'); ?>>Lift Up</option>
                            <option value="scale" <?php selected($card_hover_effect, 'scale'); ?>>Scale Up</option>
                            <option value="glow" <?php selected($card_hover_effect, 'glow'); ?>>Glow</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Card Shadow</th>
                    <td>
                        <input type="text" name="kb_card_shadow" value="<?php echo esc_attr($card_shadow); ?>" class="regular-text">
                        <p class="description">e.g., 0 2px 4px rgba(0,0,0,0.1)</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Card Hover Shadow</th>
                    <td>
                        <input type="text" name="kb_card_hover_shadow" value="<?php echo esc_attr($card_hover_shadow); ?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Transition Speed (seconds)</th>
                    <td>
                        <input type="number" name="kb_transition_speed" value="<?php echo esc_attr($transition_speed); ?>" min="0" max="2" step="0.1">
                    </td>
                </tr>
            </table>
            
            <h2>Card Icon Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Icon Size (px)</th>
                    <td>
                        <input type="number" name="kb_card_icon_size" value="<?php echo esc_attr($card_icon_size); ?>" min="20" max="100">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Icon Background Color</th>
                    <td>
                        <input type="text" name="kb_card_icon_bg" value="<?php echo esc_attr($card_icon_bg); ?>" class="color-picker">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Icon Color</th>
                    <td>
                        <input type="text" name="kb_card_icon_color" value="<?php echo esc_attr($card_icon_color); ?>" class="color-picker">
                    </td>
                </tr>
            </table>
            
            <h2>Card Typography</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Card Title Size (px)</th>
                    <td>
                        <input type="number" name="kb_card_title_size" value="<?php echo esc_attr($card_title_size); ?>" min="14" max="36">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Card Description Size (px)</th>
                    <td>
                        <input type="number" name="kb_card_desc_size" value="<?php echo esc_attr($card_desc_size); ?>" min="10" max="24">
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" name="kb_save_cards" class="button button-primary">💾 Save Card Settings</button>
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.color-picker').wpColorPicker();
    });
    </script>
    <?php
}

// Search & Breadcrumb Page
function kb_search_page() {
    if (!current_user_can('manage_options')) return;
    
    if (isset($_POST['kb_save_search'])) {
        check_admin_referer('kb_search_nonce');
        
        update_option('kb_search_placeholder', sanitize_text_field($_POST['kb_search_placeholder']));
        update_option('kb_search_bg_color', sanitize_hex_color($_POST['kb_search_bg_color']));
        update_option('kb_search_text_color', sanitize_hex_color($_POST['kb_search_text_color']));
        update_option('kb_search_border_radius', intval($_POST['kb_search_border_radius']));
        update_option('kb_search_button_text', sanitize_text_field($_POST['kb_search_button_text']));
        update_option('kb_breadcrumb_separator', sanitize_text_field($_POST['kb_breadcrumb_separator']));
        update_option('kb_breadcrumb_color', sanitize_hex_color($_POST['kb_breadcrumb_color']));
        
        echo '<div class="notice notice-success"><p>✅ Search settings saved!</p></div>';
    }
    
    $search_placeholder = get_option('kb_search_placeholder', 'Search for articles, guides, tutorials...');
    $search_bg_color = get_option('kb_search_bg_color', '#ffffff');
    $search_text_color = get_option('kb_search_text_color', '#1f2937');
    $search_border_radius = get_option('kb_search_border_radius', 10);
    $search_button_text = get_option('kb_search_button_text', 'Search');
    $breadcrumb_separator = get_option('kb_breadcrumb_separator', '/');
    $breadcrumb_color = get_option('kb_breadcrumb_color', '#ffffff');
    ?>
    
    <div class="wrap">
        <h1>🔍 Search & Breadcrumb</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_search_nonce'); ?>
            
            <h2>Search Bar Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Search Placeholder</th>
                    <td>
                        <input type="text" name="kb_search_placeholder" value="<?php echo esc_attr($search_placeholder); ?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Search Background Color</th>
                    <td>
                        <input type="text" name="kb_search_bg_color" value="<?php echo esc_attr($search_bg_color); ?>" class="color-picker">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Search Text Color</th>
                    <td>
                        <input type="text" name="kb_search_text_color" value="<?php echo esc_attr($search_text_color); ?>" class="color-picker">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Search Border Radius (px)</th>
                    <td>
                        <input type="number" name="kb_search_border_radius" value="<?php echo esc_attr($search_border_radius); ?>" min="0" max="50">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Search Button Text</th>
                    <td>
                        <input type="text" name="kb_search_button_text" value="<?php echo esc_attr($search_button_text); ?>">
                    </td>
                </tr>
            </table>
            
            <h2>Breadcrumb Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Breadcrumb Separator</th>
                    <td>
                        <input type="text" name="kb_breadcrumb_separator" value="<?php echo esc_attr($breadcrumb_separator); ?>" maxlength="3">
                        <p class="description">e.g., /, >, →, •</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Breadcrumb Color</th>
                    <td>
                        <input type="text" name="kb_breadcrumb_color" value="<?php echo esc_attr($breadcrumb_color); ?>" class="color-picker">
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" name="kb_save_search" class="button button-primary">💾 Save Search Settings</button>
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.color-picker').wpColorPicker();
    });
    </script>
    <?php
}

// Footer Page
function kb_footer_page() {
    if (!current_user_can('manage_options')) return;
    
    if (isset($_POST['kb_save_footer'])) {
        check_admin_referer('kb_footer_nonce');
        
        update_option('kb_footer_bg_color', sanitize_hex_color($_POST['kb_footer_bg_color']));
        update_option('kb_footer_text_color', sanitize_hex_color($_POST['kb_footer_text_color']));
        update_option('kb_footer_text', wp_kses_post($_POST['kb_footer_text']));
        update_option('kb_footer_columns', intval($_POST['kb_footer_columns']));
        update_option('kb_show_footer_categories', isset($_POST['kb_show_footer_categories']));
        
        echo '<div class="notice notice-success"><p>✅ Footer saved!</p></div>';
    }
    
    $footer_bg_color = get_option('kb_footer_bg_color', '#1f2937');
    $footer_text_color = get_option('kb_footer_text_color', '#ffffff');
    $footer_text = get_option('kb_footer_text', '© ' . date('Y') . ' ' . get_bloginfo('name'));
    $footer_columns = get_option('kb_footer_columns', 4);
    $show_footer_categories = get_option('kb_show_footer_categories', true);
    ?>
    
    <div class="wrap">
        <h1>🦶 Footer Settings</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_footer_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Footer Background Color</th>
                    <td>
                        <input type="text" name="kb_footer_bg_color" value="<?php echo esc_attr($footer_bg_color); ?>" class="color-picker">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Footer Text Color</th>
                    <td>
                        <input type="text" name="kb_footer_text_color" value="<?php echo esc_attr($footer_text_color); ?>" class="color-picker">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Footer Copyright Text</th>
                    <td>
                        <textarea name="kb_footer_text" rows="3" class="large-text"><?php echo esc_textarea($footer_text); ?></textarea>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Footer Columns</th>
                    <td>
                        <select name="kb_footer_columns">
                            <option value="2" <?php selected($footer_columns, 2); ?>>2 Columns</option>
                            <option value="3" <?php selected($footer_columns, 3); ?>>3 Columns</option>
                            <option value="4" <?php selected($footer_columns, 4); ?>>4 Columns</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Show Categories in Footer</th>
                    <td>
                        <label>
                            <input type="checkbox" name="kb_show_footer_categories" <?php checked($show_footer_categories); ?>>
                            Display category links in footer
                        </label>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" name="kb_save_footer" class="button button-primary">💾 Save Footer</button>
            </p>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.color-picker').wpColorPicker();
    });
    </script>
    <?php
}

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
        
        <form method="post" action="">
            <?php wp_nonce_field('kb_visibility_nonce'); ?>
            
            <div class="kb-admin-panel">
                <div class="kb-admin-section">
                    <h3>Hide Posts</h3>
                    <p>Select posts you want to hide from the Knowledge Base:</p>
                    
                    <?php foreach ($posts as $post): ?>
                        <div class="kb-visibility-toggle">
                            <input type="checkbox" 
                                   name="kb_hidden_posts[]" 
                                   value="<?php echo esc_attr($post->ID); ?>"
                                   id="post-<?php echo esc_attr($post->ID); ?>"
                                   <?php checked(in_array($post->ID, $hidden_posts)); ?>>
                            <label for="post-<?php echo esc_attr($post->ID); ?>">
                                <?php echo esc_html($post->post_title); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="kb-admin-section">
                    <h3>Hide Categories</h3>
                    <p>Select categories you want to hide from the Knowledge Base:</p>
                    
                    <?php foreach ($categories as $category): ?>
                        <div class="kb-visibility-toggle">
                            <input type="checkbox" 
                                   name="kb_hidden_categories[]" 
                                   value="<?php echo esc_attr($category->term_id); ?>"
                                   id="cat-<?php echo esc_attr($category->term_id); ?>"
                                   <?php checked(in_array($category->term_id, $hidden_categories)); ?>>
                            <label for="cat-<?php echo esc_attr($category->term_id); ?>">
                                <?php echo esc_html($category->name); ?> (<?php echo esc_html($category->count); ?> posts)
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <button type="submit" name="kb_save_visibility" class="button button-primary">💾 Save Visibility Settings</button>
            </div>
        </form>
    </div>
    <?php
}

// Import/Export Page
function kb_import_export_page() {
    if (!current_user_can('manage_options')) return;
    
    // Export Settings
    if (isset($_POST['kb_export_settings'])) {
        check_admin_referer('kb_export_nonce');
        
        $settings = array();
        global $wpdb;
        $options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'kb_%'");
        
        foreach ($options as $option) {
            $settings[$option->option_name] = maybe_unserialize($option->option_value);
        }
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="kb-settings-' . date('Y-m-d') . '.json"');
        echo json_encode($settings, JSON_PRETTY_PRINT);
        exit;
    }
    
    // Import Settings
    if (isset($_POST['kb_import_settings'])) {
        check_admin_referer('kb_import_nonce');
        
        if (isset($_FILES['kb_import_file'])) {
            $file = $_FILES['kb_import_file']['tmp_name'];
            $json = file_get_contents($file);
            $settings = json_decode($json, true);
            
            if ($settings) {
                foreach ($settings as $key => $value) {
                    update_option($key, $value);
                }
                echo '<div class="notice notice-success"><p>✅ Settings imported successfully!</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>❌ Invalid file format!</p></div>';
            }
        }
    }
    ?>
    
    <div class="wrap">
        <h1>📦 Import/Export Settings</h1>
        
        <div class="kb-admin-panel">
            <h2>Export Settings</h2>
            <p>Download all theme settings as a JSON file:</p>
            <form method="post" action="">
                <?php wp_nonce_field('kb_export_nonce'); ?>
                <button type="submit" name="kb_export_settings" class="button button-primary">⬇️ Export Settings</button>
            </form>
        </div>
        
        <div class="kb-admin-panel" style="margin-top: 30px;">
            <h2>Import Settings</h2>
            <p>Upload a previously exported JSON file to restore settings:</p>
            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field('kb_import_nonce'); ?>
                <input type="file" name="kb_import_file" accept=".json" required>
                <br><br>
                <button type="submit" name="kb_import_settings" class="button button-primary">⬆️ Import Settings</button>
            </form>
        </div>
    </div>
    <?php
}

// Apply Custom Styles
function kb_apply_custom_styles() {
    if (!is_plugin_activated5929()) {
        return;
    }

    // Get all options
    $primary_color = get_option('kb_primary_color', '#2563eb');
    $secondary_color = get_option('kb_secondary_color', '#10b981');
    $text_color = get_option('kb_text_color', '#1f2937');
    $bg_color = get_option('kb_bg_color', '#ffffff');
    $card_bg = get_option('kb_card_bg', '#f9fafb');
    $border_color = get_option('kb_border_color', '#e5e7eb');
    $gradient_start = get_option('kb_gradient_start', '#2563eb');
    $gradient_end = get_option('kb_gradient_end', '#7c3aed');
    $gradient_direction = get_option('kb_gradient_direction', '135deg');
    $link_color = get_option('kb_link_color', '#2563eb');
    $link_hover_color = get_option('kb_link_hover_color', '#1d4ed8');
    
    // Typography
    $font_family = get_option('kb_font_family', 'System Default');
    $custom_font_url = get_option('kb_custom_font_url', '');
    $font_size_base = get_option('kb_font_size_base', 16);
    $font_size_h1 = get_option('kb_font_size_h1', 36);
    $font_size_h2 = get_option('kb_font_size_h2', 28);
    $font_size_h3 = get_option('kb_font_size_h3', 22);
    $line_height = get_option('kb_line_height', 1.6);
    $font_weight_normal = get_option('kb_font_weight_normal', 400);
    $font_weight_bold = get_option('kb_font_weight_bold', 700);
    $letter_spacing = get_option('kb_letter_spacing', 0);
    
    // Layout
    $container_width = get_option('kb_container_width', 1200);
    $section_padding_top = get_option('kb_section_padding_top', 40);
    $section_padding_bottom = get_option('kb_section_padding_bottom', 40);
    $card_gap = get_option('kb_card_gap', 20);
    $card_padding = get_option('kb_card_padding', 24);
    $card_border_radius = get_option('kb_card_border_radius', 12);
    $card_border_width = get_option('kb_card_border_width', 1);
    $button_padding_x = get_option('kb_button_padding_x', 20);
    $button_padding_y = get_option('kb_button_padding_y', 10);
    $button_border_radius = get_option('kb_button_border_radius', 6);
    
    // Header
    $header_bg_color = get_option('kb_header_bg_color', '#ffffff');
    $header_text_color = get_option('kb_header_text_color', '#1f2937');
    $header_height = get_option('kb_header_height', 60);
    $header_sticky = get_option('kb_header_sticky', true);
    $nav_font_size = get_option('kb_nav_font_size', 14);
    $nav_gap = get_option('kb_nav_gap', 25);
    
    // Cards
    $card_hover_effect = get_option('kb_card_hover_effect', 'lift');
    $card_shadow = get_option('kb_card_shadow', '0 2px 4px rgba(0,0,0,0.1)');
    $card_hover_shadow = get_option('kb_card_hover_shadow', '0 10px 20px rgba(0,0,0,0.1)');
    $card_icon_size = get_option('kb_card_icon_size', 48);
    $card_icon_bg = get_option('kb_card_icon_bg', '#2563eb');
    $card_icon_color = get_option('kb_card_icon_color', '#ffffff');
    $card_title_size = get_option('kb_card_title_size', 20);
    $card_desc_size = get_option('kb_card_desc_size', 14);
    $transition_speed = get_option('kb_transition_speed', 0.3);
    
    // Search
    $search_placeholder = get_option('kb_search_placeholder', 'Search for articles, guides, tutorials...');
    $search_bg_color = get_option('kb_search_bg_color', '#ffffff');
    $search_text_color = get_option('kb_search_text_color', '#1f2937');
    $search_border_radius = get_option('kb_search_border_radius', 10);
    $breadcrumb_separator = get_option('kb_breadcrumb_separator', '/');
    $breadcrumb_color = get_option('kb_breadcrumb_color', '#ffffff');
    
    // Footer
    $footer_bg_color = get_option('kb_footer_bg_color', '#1f2937');
    $footer_text_color = get_option('kb_footer_text_color', '#ffffff');
    
    // Background
    $bg_image = get_option('kb_bg_image', '');
    $bg_opacity = get_option('kb_bg_opacity', 1);
    $bg_attachment = get_option('kb_bg_attachment', 'scroll');
    $bg_size = get_option('kb_bg_size', 'cover');
    
    // Import custom font if needed
    if ($font_family === 'Custom' && !empty($custom_font_url)) {
        echo '<link rel="stylesheet" href="' . esc_url($custom_font_url) . '">';
    } elseif (strpos($font_family, 'Google Font') !== false || in_array($font_family, ["'Roboto', sans-serif", "'Open Sans', sans-serif", "'Lato', sans-serif", "'Montserrat', sans-serif", "'Poppins', sans-serif"])) {
        $font_name = str_replace(["'", ', sans-serif', ', serif'], '', $font_family);
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        echo '<link href="https://fonts.googleapis.com/css2?family=' . urlencode($font_name) . ':wght@300;400;500;600;700;800&display=swap" rel="stylesheet">';
    }
    
    $font_family_css = ($font_family === 'System Default') ? '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif' : $font_family;
    
    ?>
    <style>
        :root {
            /* Colors */
            --kb-primary-color: <?php echo esc_attr($primary_color); ?>;
            --kb-secondary-color: <?php echo esc_attr($secondary_color); ?>;
            --kb-text-color: <?php echo esc_attr($text_color); ?>;
            --kb-bg-color: <?php echo esc_attr($bg_color); ?>;
            --kb-card-bg: <?php echo esc_attr($card_bg); ?>;
            --kb-border-color: <?php echo esc_attr($border_color); ?>;
            --kb-gradient-start: <?php echo esc_attr($gradient_start); ?>;
            --kb-gradient-end: <?php echo esc_attr($gradient_end); ?>;
            --kb-link-color: <?php echo esc_attr($link_color); ?>;
            --kb-link-hover-color: <?php echo esc_attr($link_hover_color); ?>;
            
            /* Typography */
            --kb-font-family: <?php echo $font_family_css; ?>;
            --kb-font-size-base: <?php echo esc_attr($font_size_base); ?>px;
            --kb-font-size-h1: <?php echo esc_attr($font_size_h1); ?>px;
            --kb-font-size-h2: <?php echo esc_attr($font_size_h2); ?>px;
            --kb-font-size-h3: <?php echo esc_attr($font_size_h3); ?>px;
            --kb-line-height: <?php echo esc_attr($line_height); ?>;
            --kb-font-weight-normal: <?php echo esc_attr($font_weight_normal); ?>;
            --kb-font-weight-bold: <?php echo esc_attr($font_weight_bold); ?>;
            --kb-letter-spacing: <?php echo esc_attr($letter_spacing); ?>px;
            
            /* Layout */
            --kb-container-width: <?php echo esc_attr($container_width); ?>px;
            --kb-section-padding-top: <?php echo esc_attr($section_padding_top); ?>px;
            --kb-section-padding-bottom: <?php echo esc_attr($section_padding_bottom); ?>px;
            --kb-card-gap: <?php echo esc_attr($card_gap); ?>px;
            --kb-card-padding: <?php echo esc_attr($card_padding); ?>px;
            --kb-card-border-radius: <?php echo esc_attr($card_border_radius); ?>px;
            --kb-card-border-width: <?php echo esc_attr($card_border_width); ?>px;
            --kb-button-padding-x: <?php echo esc_attr($button_padding_x); ?>px;
            --kb-button-padding-y: <?php echo esc_attr($button_padding_y); ?>px;
            --kb-button-border-radius: <?php echo esc_attr($button_border_radius); ?>px;
            
            /* Header */
            --kb-header-bg-color: <?php echo esc_attr($header_bg_color); ?>;
            --kb-header-text-color: <?php echo esc_attr($header_text_color); ?>;
            --kb-header-height: <?php echo esc_attr($header_height); ?>px;
            --kb-nav-font-size: <?php echo esc_attr($nav_font_size); ?>px;
            --kb-nav-gap: <?php echo esc_attr($nav_gap); ?>px;
            
            /* Cards */
            --kb-card-shadow: <?php echo esc_attr($card_shadow); ?>;
            --kb-card-hover-shadow: <?php echo esc_attr($card_hover_shadow); ?>;
            --kb-card-icon-size: <?php echo esc_attr($card_icon_size); ?>px;
            --kb-card-icon-bg: <?php echo esc_attr($card_icon_bg); ?>;
            --kb-card-icon-color: <?php echo esc_attr($card_icon_color); ?>;
            --kb-card-title-size: <?php echo esc_attr($card_title_size); ?>px;
            --kb-card-desc-size: <?php echo esc_attr($card_desc_size); ?>px;
            --kb-transition-speed: <?php echo esc_attr($transition_speed); ?>s;
            
            /* Search */
            --kb-search-bg-color: <?php echo esc_attr($search_bg_color); ?>;
            --kb-search-text-color: <?php echo esc_attr($search_text_color); ?>;
            --kb-search-border-radius: <?php echo esc_attr($search_border_radius); ?>px;
            --kb-breadcrumb-color: <?php echo esc_attr($breadcrumb_color); ?>;
            
            /* Footer */
            --kb-footer-bg-color: <?php echo esc_attr($footer_bg_color); ?>;
            --kb-footer-text-color: <?php echo esc_attr($footer_text_color); ?>;
        }
        
        /* Apply Background Image */
        <?php if ($bg_image): ?>
        body {
            background-image: url('<?php echo esc_url($bg_image); ?>');
            background-size: <?php echo esc_attr($bg_size); ?>;
            background-attachment: <?php echo esc_attr($bg_attachment); ?>;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, <?php echo esc_attr(1 - $bg_opacity); ?>);
            z-index: -1;
        }
        <?php endif; ?>
        
        /* Update all elements to use CSS variables */
        body {
            font-size: var(--kb-font-size-base);
            line-height: var(--kb-line-height);
            font-weight: var(--kb-font-weight-normal);
            letter-spacing: var(--kb-letter-spacing);
        }
        
        h1 { font-size: var(--kb-font-size-h1); font-weight: var(--kb-font-weight-bold); }
        h2 { font-size: var(--kb-font-size-h2); font-weight: var(--kb-font-weight-bold); }
        h3 { font-size: var(--kb-font-size-h3); font-weight: var(--kb-font-weight-bold); }
        
        .kb-container {
            max-width: var(--kb-container-width);
        }
        
        .kb-header {
            background: linear-gradient(<?php echo esc_attr($gradient_direction); ?>, var(--kb-gradient-start), var(--kb-gradient-end));
            padding: var(--kb-section-padding-top) 0;
        }
        
        .kb-cards-grid {
            gap: var(--kb-card-gap);
            padding: var(--kb-section-padding-top) 0 var(--kb-section-padding-bottom);
        }
        
        .kb-card {
            padding: var(--kb-card-padding);
            border-radius: var(--kb-card-border-radius);
            border-width: var(--kb-card-border-width);
            box-shadow: var(--kb-card-shadow);
            transition: all var(--kb-transition-speed) ease;
        }
        
        .kb-card:hover {
            box-shadow: var(--kb-card-hover-shadow);
            <?php if ($card_hover_effect === 'lift'): ?>
            transform: translateY(-5px);
            <?php elseif ($card_hover_effect === 'scale'): ?>
            transform: scale(1.05);
            <?php elseif ($card_hover_effect === 'glow'): ?>
            box-shadow: 0 0 20px var(--kb-primary-color);
            <?php endif; ?>
        }
        
        .kb-card-icon {
            width: var(--kb-card-icon-size);
            height: var(--kb-card-icon-size);
            background: var(--kb-card-icon-bg);
            color: var(--kb-card-icon-color);
            border-radius: calc(var(--kb-card-border-radius) - 2px);
        }
        
        .kb-card-title {
            font-size: var(--kb-card-title-size);
        }
        
        .kb-card-description {
            font-size: var(--kb-card-desc-size);
        }
        
        .kb-search-input {
            background: var(--kb-search-bg-color);
            color: var(--kb-search-text-color);
            border-radius: var(--kb-search-border-radius);
        }
        
        .kb-search-submit {
            padding: var(--kb-button-padding-y) var(--kb-button-padding-x);
            border-radius: var(--kb-button-border-radius);
        }
        
        .kb-breadcrumb {
            color: var(--kb-breadcrumb-color);
        }
        
        .kb-breadcrumb span:not(:first-child):not(:last-child) {
            margin: 0 8px;
        }
        
        .kb-breadcrumb span:not(:first-child):not(:last-child)::before {
            content: '<?php echo esc_js($breadcrumb_separator); ?>';
        }
        
        nav {
            background: var(--kb-header-bg-color) !important;
            color: var(--kb-header-text-color) !important;
            <?php if ($header_sticky): ?>
            position: sticky !important;
            top: 0;
            <?php endif; ?>
        }
        
        nav a {
            font-size: var(--kb-nav-font-size);
            color: var(--kb-header-text-color) !important;
        }
        
        nav ul {
            gap: var(--kb-nav-gap) !important;
        }
        
        footer {
            background: var(--kb-footer-bg-color) !important;
            color: var(--kb-footer-text-color) !important;
        }
        
        a {
            color: var(--kb-link-color);
        }
        
        a:hover {
            color: var(--kb-link-hover-color);
        }
        
        .button, .kb-tab {
            padding: var(--kb-button-padding-y) var(--kb-button-padding-x);
            border-radius: var(--kb-button-border-radius);
            transition: all var(--kb-transition-speed) ease;
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

// AJAX: Get posts by category
function kb_ajax_get_posts() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kb_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
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
            $response[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'excerpt' => wp_trim_words(get_the_excerpt($post->ID), 20),
                'link' => get_permalink($post->ID),
                'categories' => wp_get_post_categories($post->ID)
            );
        }
    }
    
    wp_send_json_success($response);
}
add_action('wp_ajax_kb_get_posts', 'kb_ajax_get_posts');
add_action('wp_ajax_nopriv_kb_get_posts', 'kb_ajax_get_posts');

// AJAX: Search posts
function kb_search_posts() {
    $search_query = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    $args = array(
        's' => $search_query,
        'posts_per_page' => 10,
        'post_status' => 'publish',
        'post_type' => 'post' // <-- अपने CPT से बदलें
    );

    $search_query_obj = new WP_Query($args);

    $response = array();

    if ($search_query_obj->have_posts()) {
        while ($search_query_obj->have_posts()) {
            $search_query_obj->the_post();
            $response[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'excerpt' => wp_trim_words(get_the_excerpt(), 25),
                'link' => get_permalink()
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success($response);
}

add_action('wp_ajax_kb_search', 'kb_search_posts');
add_action('wp_ajax_nopriv_kb_search', 'kb_search_posts');