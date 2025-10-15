<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

<div class="kb-header" style="padding: 30px 0;">
    <div class="kb-container">
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
        
        <div class="kb-search-wrapper">
            <form class="kb-search-form" id="kb-search-form">
                <input type="text" 
                       class="kb-search-input" 
                       id="kb-search-input"
                       placeholder="Search for articles, guides, tutorials..." 
                       autocomplete="off">
                <button type="submit" class="kb-search-submit">Search</button>
            </form>
            <div id="kb-search-results" style="display: none; background: white; margin-top: 10px; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
        </div>
    </div>
</div>

<div class="kb-single-post">
    <div class="kb-container">
        <article style="display: flex; flex-direction: column; gap: 20px; align-items: flex-start;">
            
            <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                <h1 class="kb-post-title"><?php the_title(); ?></h1>
                <div class="kb-post-meta">
                    <span>Published on <?php echo get_the_date('F j, Y'); ?></span>
                    <span style="margin: 0 10px;">•</span>
                    <span>By <?php echo get_the_author(); ?></span>
                    <?php if (!empty($categories)): ?>
                        <span style="margin: 0 10px;">•</span>
                        <span>in <?php echo esc_html($category->name); ?></span>
                    <?php endif; ?>
                </div>
            </div>
           
            <div class="kb-post-content">
                <?php the_content(); ?>
            </div>
           
            <div style="margin-top: 60px; padding-top: 40px; border-top: 2px solid var(--kb-border-color); width: 100%;">
                <h2 style="margin-bottom: 30px;">Related Articles</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                    <?php
                    $related_args = array(
                        'category__in' => wp_get_post_categories(get_the_ID()),
                        'post__not_in' => array(get_the_ID()),
                        'posts_per_page' => 3,
                        'orderby' => 'rand'
                    );
                    $related_query = new WP_Query($related_args);
                    
                    if ($related_query->have_posts()):
                        while ($related_query->have_posts()): $related_query->the_post();
                    ?>
                        <div style="background: var(--kb-card-bg); border-radius: 10px; padding: 20px; transition: transform 0.2s;">
                            <h3 style="margin-bottom: 10px; font-size: 18px;">
                                <a href="<?php the_permalink(); ?>" style="color: inherit; text-decoration: none;">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                            <p style="font-size: 14px; color: #666; margin-bottom: 10px;">
                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                            </p>
                            <a href="<?php the_permalink(); ?>" style="color: var(--kb-primary-color); font-size: 14px; text-decoration: none;">
                                Read More →
                            </a>
                        </div>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else:
                    ?>
                        <p>No related articles found.</p>
                    <?php endif; ?>
                </div>
            </div>
           
            <div style="margin-top: 40px; padding: 20px; background: var(--kb-card-bg); border-radius: 10px; width: 100%;">
                <p style="margin-bottom: 15px; font-size: 16px; text-align: center;">Was this article helpful?</p>
                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <button onclick="kbFeedback(true)" style="padding: 10px 24px; background: var(--kb-primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; transition: opacity 0.2s;">
                        👍 Yes
                    </button>
                    <button onclick="kbFeedback(false)" style="padding: 10px 24px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; transition: opacity 0.2s;">
                        👎 No
                    </button>
                    <button onclick="window.print()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; transition: background 0.2s;">
                        🖨️ Print
                    </button>
                    <button onclick="navigator.share ? navigator.share({title: '<?php echo esc_js(get_the_title()); ?>', url: '<?php echo esc_url(get_permalink()); ?>'}) : alert('Sharing not supported')" style="padding: 10px 20px; background: var(--kb-primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; transition: background 0.2s;">
                        📤 Share
                    </button>
                </div>
            </div>
            
        </article>
    </div>
</div>

<script>
function kbFeedback(isPositive) {
    const message = isPositive ? 'Thank you for your feedback!' : 'Thanks for letting us know. We\'ll work on improving this article.';
    alert(message);
}
</script>

<?php endwhile; ?>

<?php get_footer(); ?>
