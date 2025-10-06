<?php
/**
 * The template for displaying category archives
 */
get_header();

$category = get_queried_object();
?>

<div class="kb-header">
    <div class="kb-container">
        <!-- Breadcrumb -->
        <div class="kb-breadcrumb">
            <a href="<?php echo home_url(); ?>">Home</a>
            <span>/</span>
            <a href="<?php echo home_url(); ?>">Knowledge Base</a>
            <?php if ($category->parent): 
                $parent = get_category($category->parent);
            ?>
                <span>/</span>
                <a href="<?php echo get_category_link($parent->term_id); ?>"><?php echo esc_html($parent->name); ?></a>
            <?php endif; ?>
            <span>/</span>
            <span><?php echo esc_html($category->name); ?></span>
        </div>
        
        <h1 style="font-size: 42px; margin-bottom: 10px;"><?php echo esc_html($category->name); ?></h1>
        <?php if ($category->description): ?>
            <p style="font-size: 18px; opacity: 0.9;"><?php echo esc_html($category->description); ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="kb-container">
    <?php
    // Check for subcategories
    $subcategories = get_categories(array(
        'parent' => $category->term_id,
        'hide_empty' => false
    ));
    
    // Filter visible subcategories
    $visible_subcategories = array_filter($subcategories, function($subcat) {
        return kb_is_visible($subcat->term_id, 'category');
    });
    
    if (!empty($visible_subcategories)):
    ?>
        <div style="margin: 40px 0;">
            <h2 style="font-size: 24px; margin-bottom: 20px;">Subcategories</h2>
            <div class="kb-cards-grid">
                <?php foreach ($visible_subcategories as $subcategory): 
                    $post_count = $subcategory->count;
                ?>
                    <div class="kb-card" onclick="location.href='<?php echo get_category_link($subcategory->term_id); ?>'">
                        <div class="kb-card-icon">📁</div>
                        <h3 class="kb-card-title"><?php echo esc_html($subcategory->name); ?></h3>
                        <p class="kb-card-description"><?php echo esc_html($subcategory->description ?: 'Browse articles in this category'); ?></p>
                        <span class="kb-card-count"><?php echo esc_html($post_count); ?> articles</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (have_posts()): ?>
        <div style="margin: 40px 0;">
            <h2 style="font-size: 24px; margin-bottom: 20px;">Articles in this Category</h2>
            <div class="kb-cards-grid">
                <?php 
                $hidden_posts = get_option('kb_hidden_posts', array());
                
                while (have_posts()): 
                    the_post();
                    
                    // Skip hidden posts
                    if (in_array(get_the_ID(), $hidden_posts)) continue;
                ?>
                    <div class="kb-card" onclick="location.href='<?php the_permalink(); ?>'">
                        <div class="kb-card-icon">📄</div>
                        <h3 class="kb-card-title"><?php the_title(); ?></h3>
                        <p class="kb-card-description"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?></p>
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--kb-border-color); font-size: 13px; color: #6b7280;">
                            <span><?php echo get_the_date(); ?></span>
                            <span style="margin: 0 8px;">•</span>
                            <span><?php the_author(); ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <?php
            // Pagination
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => '← Previous',
                'next_text' => 'Next →',
            ));
            ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <h2 style="font-size: 24px; margin-bottom: 15px;">No Articles Found</h2>
            <p style="color: #6b7280; margin-bottom: 25px;">There are no articles in this category yet.</p>
            <a href="<?php echo home_url(); ?>" style="padding: 12px 30px; background: var(--kb-primary-color); color: white; border-radius: 6px; text-decoration: none; display: inline-block;">
                Browse All Categories
            </a>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>