<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function add_keywords_meta_tag() {
    if (is_single()) { // only add on single posts
        global $post;
        
        // Option 1: use post tags as keywords
        $tags = wp_get_post_tags($post->ID, array('fields' => 'names'));
        if ($tags) {
            $keywords = implode(',', $tags);
            echo '<meta name="keywords" content="' . esc_attr($keywords) . '">' . "\n";
        }

        // Option 2: use custom field 'meta_keywords'
        $custom_keywords = get_post_meta($post->ID, 'meta_keywords', true);
        if ($custom_keywords) {
            echo '<meta name="keywords" content="' . esc_attr($custom_keywords) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'add_keywords_meta_tag');
