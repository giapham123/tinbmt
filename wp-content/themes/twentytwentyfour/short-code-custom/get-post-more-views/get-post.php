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

        // Thumbnail
        $thumbnail = has_post_thumbnail()
            ? get_the_post_thumbnail(get_the_ID(), 'thumbnail', ['class' => 'top-view-thumb'])
            : '<div class="top-view-thumb default-thumb"></div>';

        // === REF + LOGO ===
        $ref   = get_post_meta(get_the_ID(), '_ref', true);
        $logos = get_source_logos_map();
        $logo  = $logos[$ref] ?? $logos['default'];

        // ============================
        // TIME AGO or dd/mm/yyyy
        // ============================
        $post_time    = get_the_time('U');
        $current_time = current_time('timestamp');
        $diff_hours   = ($current_time - $post_time) / 3600;

        if ($diff_hours <= 24) {
            // Dưới 24 giờ → time ago
            $time_display = human_time_diff($post_time, $current_time) . ' trước';
        } else {
            // Trên 24 giờ → dd/mm/yyyy
            $time_display = get_the_time('d/m/Y');
        }

        $output .= '
        <li class="top-view-item">
            <div class="thumb-wrapper">' . $thumbnail . '</div>
            <div class="info-wrapper">
                <a href="' . get_permalink() . '" class="top-view-title">' . get_the_title() . '</a>

                <div class="top-view-meta" 
                     style="display:flex;align-items:center;gap:8px;font-size:12px;color:#666;margin:4px 0;">
                    <img src="' . esc_url($logo) . '" style="height:18px;width:auto;">
                    <span>' . esc_html($time_display) . '</span>
                                    <span class="top-view-count">' . $views . ' views</span>

                </div>

            </div>
        </li>';
    }

    $output .= '</ul>';

    wp_reset_postdata();
    return $output;
}
add_shortcode('top_views_posts', 'shortcode_top_views_posts');
