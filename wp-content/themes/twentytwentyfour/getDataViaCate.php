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
        /* Hide full meta description on mobile */
        @media (max-width: 768px) {
            .category-posts .excerpt {
                display: none !important;
            }
            .category-posts .excerpt_mobile {
                display: none !important;
            }
        }

        /* Desktop: show full excerpt, hide mobile excerpt */
        .category-posts .excerpt_mobile {
            display: none;
        }
    </style>

    <?php

    if ($query->have_posts()) {
        echo '<div class="category-posts">';

        while ($query->have_posts()) {
            $query->the_post();

            $thumbnail = has_post_thumbnail()
                ? get_the_post_thumbnail(null, 'thumbnail', array('class' => 'post-thumbnail'))
                : '<img src="' . esc_url(get_template_directory_uri()) . '/path/to/default-image.jpg" class="post-thumbnail">';

            $post_date = get_the_date('j F, Y');

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
                    <div class="post-date"><?php echo $post_date; ?></div>

                    <!-- Desktop meta desc -->
                    <div class="excerpt"><?php echo esc_html($excerpt); ?></div>

                    <!-- Mobile meta desc -->
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
