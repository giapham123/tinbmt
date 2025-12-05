<?php
// Hàm tăng lượt xem
function set_post_views($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);

    if ($count == '') {
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    } else {
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

// Hook vào single post
function count_post_views() {
    if (is_single()) {
        global $post;
        set_post_views($post->ID);
    }
}
add_action('wp_head', 'count_post_views');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);