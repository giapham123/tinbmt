<?php
require_once get_template_directory() . '/short-code-custom/get-post-more-views/get-post.php';

// 3. Load CSS
function top_posts_enqueue_styles() {
    $css_file = __DIR__ . '/get-post.css';
    if (file_exists($css_file)) {
        wp_enqueue_style(
            'get-post-css',
            get_template_directory_uri() . '/short-code-custom/get-post-more-views/get-post.css',
            [],
            filemtime($css_file)
        );
    }
}
add_action('wp_enqueue_scripts', 'top_posts_enqueue_styles');
