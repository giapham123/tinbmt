<?php
// Shortcode [top_views_posts limit="5"]
function shortcode_top_views_posts($atts) {
    $atts = shortcode_atts([
        'limit' => 5
    ], $atts);

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => intval($atts['limit']),
        'meta_key'       => 'post_views_count',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC'
    ];

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p>Không có bài viết.</p>';
    }

    $output = '<ul class="top-view-posts">';

    while ($query->have_posts()) {
        $query->the_post();

        $views = get_post_meta(get_the_ID(), 'post_views_count', true);
        if (!$views) $views = 0;

        $thumbnail = '';
        if (has_post_thumbnail()) {
            $thumbnail = get_the_post_thumbnail(get_the_ID(), 'thumbnail', ['class' => 'top-view-thumb']);
        } else {
            $thumbnail = '<div class="top-view-thumb default-thumb"></div>';
        }

        $output .= '
        <li class="top-view-item">
            <div class="thumb-wrapper">' . $thumbnail . '</div>
            <div class="info-wrapper">
                <a href="' . get_permalink() . '" class="top-view-title">' . get_the_title() . '</a>
                <span class="top-view-count">' . $views . ' views</span>
            </div>
        </li>';
    }

    $output .= '</ul>';

    wp_reset_postdata();
    return $output;
}
add_shortcode('top_views_posts', 'shortcode_top_views_posts');
