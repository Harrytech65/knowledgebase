<?php get_header(); ?>

<div class="kb-header">
    <div class="kb-container">
        <div class="kb-breadcrumb">
            <a href="<?php echo home_url(); ?>">Home</a>
            <span>/</span>
            <span>Knowledge Base</span>
        </div>
        
        <h1 style="font-size: 42px; margin-bottom: 10px;">Knowledge Base</h1>
        <p style="font-size: 18px; opacity: 0.9;">Find answers, guides, and documentation</p>
        
        <div class="kb-search-wrapper">
            <form class="kb-search-form" id="kb-search-form">
                <input type="text" 
                       class="kb-search-input" 
                       id="kb-search-input"
                       placeholder="Search for articles, guides, tutorials..." 
                       autocomplete="off">
                <button type="submit" class="kb-search-submit">Search</button>
            </form>
            <div id="kb-search-results" style="display: none; background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
        </div>
    </div>
</div>

<div class="kb-tabs">
    <div class="kb-container">
        <div class="kb-tabs-list">
            <div class="kb-tab active" data-category="all">All Categories</div>
            <?php
            $categories = kb_get_visible_categories();
            foreach ($categories as $category):
                if ($category->parent == 0):
            ?>
                <div class="kb-tab" data-category="<?php echo esc_attr($category->term_id); ?>">
                    <?php echo esc_html($category->name); ?>
                </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </div>
</div>

<div class="kb-container">
    <div class="kb-cards-grid" id="kb-cards-grid">
        <?php
        $parent_categories = array_filter($categories, function($cat) {
            return $cat->parent == 0;
        });
        
        foreach ($parent_categories as $category):
            $subcategories = get_categories(array(
                'parent' => $category->term_id,
                'hide_empty' => false
            ));
            
            $visible_subcategories = array_filter($subcategories, function($subcat) {
                return kb_is_visible($subcat->term_id, 'category');
            });
            
            if (!empty($visible_subcategories)):
                foreach ($visible_subcategories as $subcategory):
                    $post_count = $subcategory->count;
        ?>
                <div class="kb-card" onclick="location.href='<?php echo get_category_link($subcategory->term_id); ?>'">
                    <div class="kb-card-icon">📁</div>
                    <h3 class="kb-card-title"><?php echo esc_html($subcategory->name); ?></h3>
                    <p class="kb-card-description"><?php echo esc_html($subcategory->description ?: 'Browse articles in this category'); ?></p>
                    <span class="kb-card-count"><?php echo esc_html($post_count); ?> articles</span>
                </div>
        <?php
                endforeach;
            else:
                $posts = get_posts(array(
                    'category' => $category->term_id,
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                ));
                
                $hidden_posts = get_option('kb_hidden_posts', array());
                
                foreach ($posts as $post):
                    if (in_array($post->ID, $hidden_posts)) continue;
        ?>
                <div class="kb-card" onclick="location.href='<?php echo get_permalink($post); ?>'">
                    <div class="kb-card-icon">📄</div>
                    <h3 class="kb-card-title"><?php echo esc_html($post->post_title); ?></h3>
                    <p class="kb-card-description"><?php echo esc_html(wp_trim_words(get_the_excerpt($post), 15)); ?></p>
                    <span class="kb-card-count">Read more →</span>
                </div>
        <?php
                endforeach;
            endif;
        endforeach;
        
        if (empty($parent_categories)):
            $all_posts = get_posts(array(
                'posts_per_page' => -1,
                'post_status' => 'publish'
            ));
            
            $hidden_posts = get_option('kb_hidden_posts', array());
            
            foreach ($all_posts as $post):
                if (in_array($post->ID, $hidden_posts)) continue;
        ?>
            <div class="kb-card" onclick="location.href='<?php echo get_permalink($post); ?>'">
                <div class="kb-card-icon">📄</div>
                <h3 class="kb-card-title"><?php echo esc_html($post->post_title); ?></h3>
                <p class="kb-card-description"><?php echo esc_html(wp_trim_words(get_the_excerpt($post), 15)); ?></p>
                <span class="kb-card-count">Read more →</span>
            </div>
        <?php
            endforeach;
        endif;
        ?>
    </div>
</div>

<?php get_footer(); ?>
