<?php
get_header();
$category = get_queried_object();
$parent_id = $category->parent;

// Check if this is parent or child category
$is_parent = ($parent_id == 0);
$is_child = ($parent_id > 0);
?>

<div class="kb-header" style="padding: 30px 0;">
    <div class="kb-container">
        <div class="kb-breadcrumb">
            <a href="<?php echo home_url(); ?>">Home</a>
            <span>/</span>
            <a href="<?php echo home_url(); ?>">knowzard</a>
            
            <?php if ($is_child):
                $parent = get_category($parent_id);
            ?>
                <span>/</span>
                <a href="<?php echo get_category_link($parent_id); ?>">
                    <?php echo esc_html($parent->name); ?>
                </a>
            <?php endif; ?>
            
            <span>/</span>
            <span><?php echo esc_html($category->name); ?></span>
        </div>
        
        <h1 style="font-size: 42px; margin-bottom: 10px;">
            <?php echo esc_html($category->name); ?>
        </h1>
        <?php if ($category->description): ?>
            <p style="font-size: 18px; opacity: 0.9;">
                <?php echo esc_html($category->description); ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="kb-container" style="padding: 60px 0;">
    
    <?php if ($is_parent): 
        // PARENT CATEGORY - Show Sub-Categories
        $subcategories = get_categories(array(
            'parent' => $category->term_id,
            'hide_empty' => false
        ));
        
        $hidden_categories = get_option('kb_hidden_categories', array());
        $visible_subcategories = array_filter($subcategories, function($subcat) use ($hidden_categories) {
            return !in_array($subcat->term_id, $hidden_categories);
        });
        
        if (!empty($visible_subcategories)):
    ?>
        <div>
            <h2 style="font-size: 24px; margin-bottom: 30px;">Subcategories</h2>
            <div class="kb-cards-grid">
                <?php foreach ($visible_subcategories as $subcategory): ?>
                    <div class="kb-card" onclick="location.href='<?php echo get_category_link($subcategory->term_id); ?>'">
                        <div class="kb-card-icon">📂</div>
                        <h3 class="kb-card-title">
                            <?php echo esc_html($subcategory->name); ?>
                        </h3>
                        <p class="kb-card-description">
                            <?php echo esc_html($subcategory->description ?: 'Browse articles in this category'); ?>
                        </p>
                        <span class="kb-card-count">
                            <?php echo esc_html($subcategory->count); ?> articles
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php 
        else:
            echo '<p style="text-align: center; color: #6b7280;">No subcategories found.</p>';
        endif;
        
    else:
        // CHILD CATEGORY - Show Posts
        $hidden_posts = get_option('kb_hidden_posts', array());
        
        if (have_posts()):
    ?>
        <div>
            <h2 style="font-size: 24px; margin-bottom: 30px;">Articles</h2>
            <div class="kb-cards-grid">
                <?php 
                while (have_posts()): 
                    the_post();
                    if (in_array(get_the_ID(), $hidden_posts)) continue;
                ?>
                    <div class="kb-card" onclick="location.href='<?php the_permalink(); ?>'">
                        <div class="kb-card-icon">📄</div>
                        <h3 class="kb-card-title"><?php the_title(); ?></h3>
                        <p class="kb-card-description">
                            <?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?>
                        </p>
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--kb-border-color); font-size: 13px; color: #6b7280;">
                            <span><?php echo get_the_date(); ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <?php
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => '← Previous',
                'next_text' => 'Next →',
            ));
            ?>
        </div>
    <?php 
        else:
    ?>
        <div style="text-align: center; padding: 60px 20px;">
            <h2 style="font-size: 24px; margin-bottom: 15px;">No Articles Found</h2>
            <p style="color: #6b7280;">This category is empty.</p>
        </div>
    <?php 
        endif;
    endif; 
    ?>
    
</div>

<?php get_footer(); ?>