<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Function to check if the plugin is activated
function is_plugin_activated5929() {
    return get_option('license_activated_5929', false);
}

// Get prefilled license data
function get_license_data5929($key) {
    return get_option("license_5929_$key", '');
}

// Add License Validator submenu under Settings
add_action('admin_menu', 'license_validator_menu5929');
function license_validator_menu5929() {
    add_submenu_page(
        'options-general.php',
        'License Validator',
        'License Validator',
        'manage_options',
        'license-validator5929',
        'license_validator_page5929'
    );
}

// Display a license activation notification under the plugin
add_filter('plugin_row_meta', 'add_license_activation_notice5929', 10, 2);
function add_license_activation_notice5929($plugin_meta, $plugin_file) {
    if ($plugin_file === plugin_basename(__FILE__) && !is_plugin_activated5929()) {
        $plugin_meta[] = '<strong style="color: red;"><a href="' . admin_url('options-general.php?page=license-validator5929') . '" style="color: red;">Please activate your license to enable updates.</a></strong>';
    }
    return $plugin_meta;
}

// Display admin notice for theme license activation
add_action('admin_notices', 'kb_theme_license_notice');
function kb_theme_license_notice() {
    if (!is_plugin_activated5929()) {
        ?>
        <div class="notice notice-error is-dismissible">
            <p>
                <strong>⚠️ knowzard Theme:</strong> 
                Please activate your license to use this theme. 
                <a href="<?php echo admin_url('options-general.php?page=license-validator5929'); ?>" style="font-weight: bold;">
                    Click here to activate →
                </a>
            </p>
        </div>
        <?php
    }
}

// Add notice in Themes page
add_action('admin_head-themes.php', 'kb_theme_page_notice');
function kb_theme_page_notice() {
    if (!is_plugin_activated5929()) {
        ?>
        <style>
            .kb-theme-warning {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                margin: 20px 0;
                padding-left:250px;
            }
        </style>
        <div class="kb-theme-warning">
            <h3>🔒 Theme License Required</h3>
            <p>The knowzard theme is currently inactive because no valid license has been found.</p>
            <p>
                <a href="<?php echo admin_url('options-general.php?page=license-validator5929'); ?>" class="button button-primary">
                    Activate License Now
                </a>
            </p>
        </div>
        <?php
    }
}
// License Validator Page
function license_validator_page5929() {
    $is_activated = is_plugin_activated5929();
    $user_email = get_license_data5929('user_email');
    $product_name = get_license_data5929('product_name');
    $user_license = get_license_data5929('user_license');
    ?>
    <div class="wrap">
        <h1>License Validator</h1>
        <form id="license-validator-form" method="post">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Email</th>
                    <td><input type="email" id="user_email" name="user_email" value="<?php echo esc_attr($user_email); ?>" required <?php echo $is_activated ? 'readonly' : ''; ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Product Name</th>
                    <td><input type="text" id="product_name" name="product_name" value="<?php echo esc_attr($product_name); ?>" required <?php echo $is_activated ? 'readonly' : ''; ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">License Code</th>
                    <td><input type="text" id="user_license" name="user_license" value="<?php echo esc_attr($user_license); ?>" required <?php echo $is_activated ? 'readonly' : ''; ?> /></td>
                </tr>
            </table>
            <?php if (!$is_activated): ?>
                <button type="button" id="validate-license" class="button button-primary">Validate License</button>
            <?php else: ?>
                <button type="submit" name="deactivate_license" class="button button-secondary">Deactivate License</button>
            <?php endif; ?>
        </form>
        <div id="license-response" style="margin-top: 20px; font-weight: bold;"></div>
    </div>
    <?php
}

// Handle license deactivation
add_action('admin_init', 'handle_license_deactivation5929');
function handle_license_deactivation5929() {
    if (isset($_POST['deactivate_license'])) {
        delete_option('license_activated_5929');
        delete_option('license_5929_user_email');
        delete_option('license_5929_product_name');
        delete_option('license_5929_user_license');
        wp_redirect(admin_url('options-general.php?page=license-validator5929'));
        exit;
    }
}

// JavaScript for License Validation
add_action('admin_footer', 'license_validation_script5929');
function license_validation_script5929() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const validateButton = document.getElementById('validate-license');
            validateButton.addEventListener('click', async function () {
                const userEmail = document.getElementById('user_email').value;
                const productName = document.getElementById('product_name').value;
                const userLicense = document.getElementById('user_license').value;
                const domain = window.location.hostname;

                const responseDiv = document.getElementById('license-response');

                try {
                    const postResponse = await fetch(ajaxurl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: 'validate_license5929',
                            user_email: userEmail,
                            product_name: productName,
                            user_license: userLicense,
                            domain: domain,
                            active: 1
                        })
                    });

                    const result = await postResponse.json();
                    responseDiv.textContent = result.message;

                    if (result.success) {
                        responseDiv.style.color = 'green';
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        responseDiv.style.color = 'red';
                    }
                } catch (error) {
                    responseDiv.textContent = 'An error occurred while validating the license.';
                    responseDiv.style.color = 'red';
                }
            });
        });
    </script>
    <?php
}

// AJAX Handler for License Validation
add_action('wp_ajax_validate_license5929', 'handle_license_validation5929');
function handle_license_validation5929() {
    $user_email = sanitize_email($_POST['user_email']);
    $product_name = sanitize_text_field($_POST['product_name']);
    $user_license = sanitize_text_field($_POST['user_license']);
    $domain = sanitize_text_field($_POST['domain']);
    $active = intval($_POST['active']);

    $post_response = wp_remote_post('https://app.myverilock.com/licenses/validate', [
        'body' => json_encode([
            'user_email' => $user_email,
            'product_name' => $product_name,
            'user_license' => $user_license,
            'domain' => $domain,
            'active' => $active
        ]),
        'headers' => [
            'Content-Type' => 'application/json'
        ]
    ]);

    if (is_wp_error($post_response)) {
        wp_send_json(['success' => false, 'message' => 'Error: Unable to connect to the API.']);
    }

    $result = json_decode(wp_remote_retrieve_body($post_response), true);

    if (isset($result['status']) && $result['status'] === 'success') {
        update_option('license_activated_5929', true);
        wp_send_json(['success' => true, 'message' => 'License validated successfully!']);
    } else {
        wp_send_json(['success' => false, 'message' => $result['message'] ?? 'Unexpected response from API.']);
    }
}
                    