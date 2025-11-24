<?php
// Add this code to your theme's functions.php file or a custom plugin
 
// Function to handle the shortcode
function category_posts_shortcode($atts) {
    // Define default attributes
    $atts = shortcode_atts(
        array(
            'category' => '', // Default to no category
            'posts_per_page' => 5, // Default number of posts to show
        ),
        $atts,
        'category_posts'
    );
 
    // Extract attributes
    $category_slug = sanitize_text_field($atts['category']);
    $posts_per_page = intval($atts['posts_per_page']);
    $paged = get_query_var('paged') ? get_query_var('paged') : 1; // Get the current page number
 
    // Prepare query arguments
    $args = array(
        'category_name'  => $category_slug,
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'orderby'        => 'date', // Order by date
        'order'          => 'DESC', // Descending order
    );
 
    // Create a new instance of WP_Query
    $query = new WP_Query($args);
 
    // Start output buffering
    ob_start();
 
    // Check if there are posts
    if ($query->have_posts()) {
        echo '<div class="category-posts">';
        // Loop through the posts
        while ($query->have_posts()) {
            $query->the_post();
           
            // Get the post thumbnail
            $thumbnail = has_post_thumbnail() ? get_the_post_thumbnail(null, 'thumbnail', array('class' => 'post-thumbnail')) : '<img src="' . esc_url(get_template_directory_uri()) . '/path/to/default-image.jpg" class="post-thumbnail" alt="Default Thumbnail">';
 
            // Get the post date
            $post_date = get_the_date('j F, Y'); // Format the date as desired
 
            // Get the post excerpt with a maximum of 100 words
            $excerpt = wp_trim_words(get_the_excerpt(), 100, '...');
            $excerpt_mobile = wp_trim_words(get_the_excerpt(), 25, '...');  
            ?>
            <div class="post">
                <div class="post-thumbnail-wrapper">
                    <a href="<?php the_permalink(); ?>"> <?php echo $thumbnail; ?></a>
                </div>
                <div class="post-content">
                    <b><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></b>
                    <div class="post-date"><?php echo $post_date; ?></div>
                    <div class="excerpt"><?php echo $excerpt; ?></div>
                    <div class="excerpt_mobile"><?php echo $excerpt_mobile; ?></div>
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
 
        // Reset post data
        wp_reset_postdata();
    } else {
        echo 'No posts found.';
    }
 
    // Get the content from the output buffer
    return ob_get_clean();
}
?>