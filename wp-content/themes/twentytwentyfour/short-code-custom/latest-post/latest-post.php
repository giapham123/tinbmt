<?php
/**
 * Shortcode: [latest_post cate="category-slug"]
 */

function custom_latest_posts_small_shortcode($atts) {

    remove_filter('the_content', 'wpautop');
    remove_filter('the_content', 'wptexturize');
    remove_filter('the_content', 'shortcode_unautop');

    // ============================
    // Shortcode attributes
    // ============================
    $atts = shortcode_atts([
        'cate' => '', // category slug
    ], $atts, 'latest_post');

    ob_start();

    // ============================
    // Query arguments
    // ============================
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    // Filter by category if provided
    if (!empty($atts['cate'])) {
        $args['category_name'] = sanitize_text_field($atts['cate']);
    }

    $q = new WP_Query($args);

    if (!$q->have_posts()) {
        return '';
    }
    ?>

    <style>
        .latest-post-shortcode * {
            margin: 5px 0 -2px !important;
            padding: 0 !important;
        }
    </style>

    <div class="latest-post-shortcode">
        <?php
        $count = 0;

        while ($q->have_posts()) :
            $q->the_post();

            // ============================
            // Source logo
            // ============================
            $ref = get_post_meta(get_the_ID(), '_ref', true);
            $source_logos = function_exists('get_source_logos_map')
                ? get_source_logos_map()
                : [];

            $source_logo = $source_logos[$ref] ?? ($source_logos['default'] ?? '');

            // ============================
            // Image
            // ============================
            $full_thumb = get_the_post_thumbnail_url(get_the_ID(), 'full');

            if (!$full_thumb) {
                $content = get_the_content();
                preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches);
                $full_thumb = $matches[1] ?? '';
            }

            $medium_thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            $display_img  = $medium_thumb ?: $full_thumb;

            // ============================
            // Time display
            // ============================
            $post_time    = get_the_time('U');
            $current_time = current_time('timestamp');
            $diff_hours   = ($current_time - $post_time) / 3600;

            if ($diff_hours <= 24) {
                $time_display = human_time_diff($post_time, $current_time) . ' trước';
            } else {
                $time_display = get_the_time('d/m/Y');
            }

            // ============================
            // Basic data
            // ============================
            $title = get_the_title();
            $link  = get_permalink();
            $cats  = get_the_category();
            $cat_name = !empty($cats) ? $cats[0]->name : 'Tin mới';
            ?>

            <?php if ($count === 0) : ?>

                <!-- FIRST BIG POST -->
                <div style="margin-bottom:14px;">

                    <?php if ($display_img) : ?>
                        <img src="<?php echo esc_url($display_img); ?>"
                             style="width:100%; border-radius:6px; margin-bottom:8px;">
                    <?php endif; ?>

                    <a href="<?php echo esc_url($link); ?>"
                       style="font-size:18px; font-weight:700; line-height:1.35; text-decoration:none; color:#000; display:block;">
                        <?php echo esc_html($title); ?>
                    </a>

                    <?php if (!empty($ref)) : ?>
                        <div style="display:flex; align-items:center; gap:16px; margin:8px 0 10px;">
                            <?php if ($source_logo) : ?>
                                <img src="<?php echo esc_url($source_logo); ?>"
                                     style="height:22px;">
                            <?php endif; ?>

                            <span style="color:#888; font-size:13px;"><?php echo esc_html($time_display); ?></span>
                            <span style="color:#888; font-size:13px;"><?php echo esc_html($cat_name); ?></span>
                        </div>
                    <?php endif; ?>

                    <div style="font-size:14px; line-height:1.45; color:#333;">
                        <?php echo esc_html(wp_trim_words(get_the_excerpt(), 30)); ?>
                    </div>
                </div>

                <hr style="border:0; border-top:1px solid #eee; margin:10px 0;">

            <?php else : ?>

                <!-- SMALL POSTS -->
                <a href="<?php echo esc_url($link); ?>"
                   style="display:flex; gap:10px; text-decoration:none; padding:6px 0; color:#000;">

                    <?php if ($display_img) : ?>
                        <img src="<?php echo esc_url($display_img); ?>"
                             style="width:90px; object-fit:cover; border-radius:4px; flex-shrink:0;">
                    <?php endif; ?>

                    <div style="flex:1; display:flex; flex-direction:column; justify-content:center;">

                        <div style="font-size:15px; font-weight:600; line-height:1.35;">
                            <?php echo esc_html($title); ?>
                        </div>

                        <?php if (!empty($ref)) : ?>
                            <div style="display:flex; align-items:center; gap:14px; margin:6px 0 4px;">
                                <?php if ($source_logo) : ?>
                                    <img src="<?php echo esc_url($source_logo); ?>"
                                         style="height:20px;">
                                <?php endif; ?>

                                <span style="color:#888; font-size:12px;"><?php echo esc_html($time_display); ?></span>
                                <span style="color:#888; font-size:12px;"><?php echo esc_html($cat_name); ?></span>
                            </div>
                        <?php endif; ?>

                    </div>
                </a>

                <?php if ($count < $q->post_count - 1) : ?>
                    <hr style="border:0; border-top:1px solid #eee; margin:10px 0;">
                <?php endif; ?>

            <?php endif; ?>

            <?php $count++; ?>
        <?php endwhile; ?>
    </div>

    <?php
    wp_reset_postdata();

    $html = ob_get_clean();
    $html = preg_replace('/<p>\s*<\/p>/i', '', $html);
    $html = str_replace(["\r", "\n"], "", $html);

    return $html;
}

add_shortcode('latest_post', 'custom_latest_posts_small_shortcode');
