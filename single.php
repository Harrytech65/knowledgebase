<?php
/**
 * The template for displaying single posts
 */
get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<div class="kb-header" style="padding: 30px 0;">
    <div class="kb-container">
        <!-- Breadcrumb -->
        <div class="kb-breadcrumb">
            <a href="<?php echo home_url(); ?>">Home</a>
            <span>/</span>
            <a href="<?php echo home_url(); ?>">Knowledge Base</a>
            <?php
            $categories = get_the_category();
            if (!empty($categories)):
                $category = $categories[0];
            ?>
                <span>/</span>
                <a href="<?php echo get_category_link($category->term_id); ?>"><?php echo esc_html($category->name); ?></a>
            <?php endif; ?>
            <span>/</span>
            <span><?php the_title(); ?></span>
        </div>
    </div>
</div>

<div class="kb-single-post">
    <article>
        <h1 class="kb-post-title"><?php the_title(); ?></h1>
        
        <div class="kb-post-meta">
            <span>Published on <?php echo get_the_date(); ?></span>
            <span style="margin: 0 10px;">•</span>
            <span>By <?php the_author(); ?></span>
            <?php if (!empty($categories)): ?>
                <span style="margin: 0 10px;">•</span>
                <span>in <?php echo esc_html($categories[0]->name); ?></span>
            <?php endif; ?>
        </div>
        
        <?php if (has_post_thumbnail()): ?>
            <div style="margin-bottom: 30px;">
                <?php the_post_thumbnail('large', array('style' => 'width: 100%; height: auto; border-radius: 12px;')); ?>
            </div>
        <?php endif; ?>
        
        <div class="kb-post-content">
            <?php the_content(); ?>
        </div>
        
        <?php
        // Related posts
        $related = get_posts(array(
            'category__in' => wp_get_post_categories($post->ID),
            'numberposts' => 3,
            'post__not_in' => array($post->ID)
        ));
        
        if (!empty($related)):
        ?>
            <div style="margin-top: 60px; padding-top: 40px; border-top: 2px solid var(--kb-border-color);">
                <h2 style="margin-bottom: 30px;">Related Articles</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                    <?php foreach ($related as $rel_post): ?>
                        <div class="kb-card" onclick="location.href='<?php echo get_permalink($rel_post); ?>'">
                            <div class="kb-card-icon">📄</div>
                            <h3 class="kb-card-title" style="font-size: 18px;"><?php echo esc_html($rel_post->post_title); ?></h3>
                            <span class="kb-card-count">Read more →</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 40px; padding: 20px; background: var(--kb-card-bg); border-radius: 10px; text-align: center;">
            <p style="margin-bottom: 15px; font-size: 16px;">Was this article helpful?</p>
            <button style="padding: 10px 24px; margin: 0 5px; background: var(--kb-primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">👍 Yes</button>
            <button style="padding: 10px 24px; margin: 0 5px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">👎 No</button>
        </div>
    </article>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>