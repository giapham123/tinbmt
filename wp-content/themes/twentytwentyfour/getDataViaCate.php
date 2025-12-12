<?php
function category_posts_shortcode($atts) {

    $atts = shortcode_atts(
        array(
            'category' => '',
            'posts_per_page' => 5,
        ),
        $atts,
        'category_posts'
    );

    $category_slug   = sanitize_text_field($atts['category']);
    $posts_per_page  = intval($atts['posts_per_page']);
    $paged           = get_query_var('paged') ? get_query_var('paged') : 1;

    $args = array(
        'category_name'  => $category_slug,
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query($args);

    ob_start();
    ?>

    <style>
        .category-posts-page-title {
            color: #d72924;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .category-posts .excerpt {
                display: none !important;
            }
        }

        .category-posts .excerpt_mobile {
            display: none;
        }

        .post-meta-line {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #666;
            margin: 4px 0;
        }

        .post-meta-line img {
            height: 18px;
            width: auto;
        }
    </style>

    <?php
    echo '<h2 class="category-posts-page-title">' . esc_html(get_the_title(get_the_ID())) . '</h2>';

    if ($query->have_posts()) {
        echo '<div class="category-posts">';

        while ($query->have_posts()) {
            $query->the_post();

            // Thumbnail
            $thumbnail = has_post_thumbnail()
                ? get_the_post_thumbnail(null, 'thumbnail', array('class' => 'post-thumbnail'))
                : '<img src="' . esc_url(get_template_directory_uri()) . '/path/to/default-image.jpg" class="post-thumbnail">';

            // ============================
            // TIME AGO (only if < 24h)
            // ============================
            $post_time    = get_the_time('U');
            $current_time = current_time('timestamp');
            $diff_hours   = ($current_time - $post_time) / 3600;

            if ($diff_hours <= 24) {
                $time_display = human_time_diff($post_time, $current_time) . ' trước';
            } else {
                $time_display = get_the_time('d/m/Y');
            }

            // REF + Logo
            $ref   = get_post_meta(get_the_ID(), '_ref', true);
            $logos = get_source_logos_map();
            $logo  = $logos[$ref] ?? $logos['default'];

            // Date
            $post_date = get_the_date('j F, Y');

            // Excerpts
            $excerpt         = wp_trim_words(get_the_excerpt(), 100, '...');
            $excerpt_mobile  = wp_trim_words(get_the_excerpt(), 25, '...');
            ?>

            <div class="post">
                <div class="post-thumbnail-wrapper">
                    <a href="<?php the_permalink(); ?>">
                        <?php echo $thumbnail; ?>
                    </a>
                </div>

                <div class="post-content">
                    <b><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></b>

                    <div class="post-meta-line">
                        <img src="<?php echo esc_url($logo); ?>" alt="logo nguồn">
                        
                        <?php if (!empty($time_display)) : ?>
                            <span><?php echo esc_html($time_display); ?></span>
                        <?php endif; ?>

                        <!-- <span><?php echo esc_html($post_date); ?></span> -->
                    </div>

                    <div class="excerpt"><?php echo esc_html($excerpt); ?></div>

                    <div class="excerpt_mobile"><?php echo esc_html($excerpt_mobile); ?></div>
                </div>
            </div>

        <?php
        }

        echo '</div>';

        // Pagination
        $total_pages = $query->max_num_pages;
        if ($total_pages > 1) {
            echo '<div class="pagination">';
            echo paginate_links(array(
                'total'   => $total_pages,
                'current' => $paged,
                'prev_text' => __('&laquo; Previous'),
                'next_text' => __('Next &raquo;'),
                'type'    => 'plain',
            ));
            echo '</div>';
        }

        wp_reset_postdata();

    } else {
        echo 'No posts found.';
    }

    return ob_get_clean();
}
?>
