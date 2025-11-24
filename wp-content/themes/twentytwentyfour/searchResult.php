<?php

function search_results_shortcode($atts) {
    ob_start(); // Start output buffering

    // Get the search query from the URL
    $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    if (!empty($search_query)) {
        $args = array(
            's' => $search_query,
            'post_type' => 'post', // You can change this to 'page' or any custom post type
            'posts_per_page' => 10,
        );
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            echo '<ul class="search-results">';

            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <li class="search-result-item">
                    <a href="<?php echo get_permalink(); ?>" class="search-result-title"><?php echo get_the_title(); ?></a>
                    <div class="date-result">
                        <?php echo get_the_date(); ?>
                    </div><!-- .entry-meta -->
                    <div class="search-result-description">
                        <?php echo wp_trim_words(get_the_excerpt(), 100, '...'); // Adjust the word limit as needed ?>
                    </div>
                </li>
                <?php
            }

            echo '</ul>';

            // Pagination
            $big = 999999999; // need an unlikely integer
            echo '<div class="pagination">';
            echo paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $query->max_num_pages
            ));
            echo '</div>';

            wp_reset_postdata();
        } else {
            echo '<p>No results found for "' . esc_html($search_query) . '"</p>';
        }
    } else {
        echo '<p>Please enter a search term.</p>';
    }

    return ob_get_clean(); // Return the buffered content
}
?>