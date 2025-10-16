<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav style="background: white; border-bottom: 1px solid var(--kb-border-color); padding: 15px 0; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div class="kb-container" style="display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 30px;">
            <?php 
            $kb_logo = get_option('kb_logo_url', '');
            
            if (!empty($kb_logo)): 
            ?>
                <a href="<?php echo home_url(); ?>" style="display: flex; align-items: center;">
                    <img src="<?php echo esc_url($kb_logo); ?>" 
                         alt="<?php bloginfo('name'); ?>" 
                         style="max-height: 60px; height: auto; width: auto;">
                </a>
            <?php elseif (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
                <a href="<?php echo home_url(); ?>" style="font-size: 24px; font-weight: 700; color: var(--kb-primary-color); text-decoration: none;">
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
            
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'kb-nav-menu',
                    'fallback_cb' => false,
                ));
            }
            ?>
        </div>
        
        <div>
            <a href="<?php echo home_url(); ?>" style="padding: 8px 20px; background: var(--kb-primary-color); color: white; border-radius: 6px; text-decoration: none; font-weight: 500;">
                knowzard
            </a>
        </div>
    </div>
</nav>