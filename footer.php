<footer style="background: var(--kb-text-color); color: white; padding: 60px 0 30px; margin-top: 80px;">
    <div class="kb-container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px;">
            <div>
                <h3 style="margin-bottom: 20px; font-size: 20px;">
                    <?php bloginfo('name'); ?>
                </h3>
                <p style="color: rgba(255,255,255,0.7); line-height: 1.6;">
                    <?php bloginfo('description'); ?>
                </p>
            </div>
            
            <div>
                <h4 style="margin-bottom: 20px; font-size: 18px;">Quick Links</h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px;">
                        <a href="<?php echo home_url(); ?>" style="color: rgba(255,255,255,0.7); text-decoration: none;">Home</a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="<?php echo home_url(); ?>" style="color: rgba(255,255,255,0.7); text-decoration: none;">Knowledge Base</a>
                    </li>
                    <?php
                    $categories = kb_get_visible_categories();
                    $count = 0;
                    foreach ($categories as $category):
                        if ($count >= 3) break;
                        if ($category->parent == 0):
                            $count++;
                    ?>
                        <li style="margin-bottom: 10px;">
                            <a href="<?php echo get_category_link($category->term_id); ?>" style="color: rgba(255,255,255,0.7); text-decoration: none;">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        </li>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </ul>
            </div>
            
            <div>
                <h4 style="margin-bottom: 20px; font-size: 18px;">Categories</h4>
                <ul style="list-style: none; padding: 0;">
                    <?php
                    $all_categories = kb_get_visible_categories();
                    $cat_count = 0;
                    foreach ($all_categories as $cat):
                        if ($cat_count >= 5) break;
                        $cat_count++;
                    ?>
                        <li style="margin-bottom: 10px;">
                            <a href="<?php echo get_category_link($cat->term_id); ?>" style="color: rgba(255,255,255,0.7); text-decoration: none;">
                                <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div>
                <h4 style="margin-bottom: 20px; font-size: 18px;">Contact</h4>
                <p style="color: rgba(255,255,255,0.7); line-height: 1.6;">
                    Have questions? Reach out to us and we'll be happy to help!
                </p>
                <div style="margin-top: 20px;">
                    <a href="mailto:<?php echo get_option('admin_email'); ?>" style="padding: 10px 20px; background: var(--kb-primary-color); color: white; border-radius: 6px; text-decoration: none; display: inline-block;">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
        
        <div style="padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); text-align: center; color: rgba(255,255,255,0.5); font-size: 14px;">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved. | Powered by Knowledge Base Theme</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>