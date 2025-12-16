<?php
if (!defined('ABSPATH')) exit;

/**
 * Enqueue CSS
 */
function tinbmt_grid_enqueue_assets() {
    wp_enqueue_style(
        'tinbmt-grid-style',
        get_template_directory_uri() . '/short-code-custom/main-post/top-post.css',
        [],
        '1.4'
    );
}
add_action('wp_enqueue_scripts', 'tinbmt_grid_enqueue_assets');

/**
 * Shortcode [tinbmt_grid]
 */
function tinbmt_grid_shortcode($atts) {

    $atts = shortcode_atts([
        'posts_per_page' => 7, // 1 main + 6 bottom
        'category'       => '',
    ], $atts);

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => intval($atts['posts_per_page']),
    ];

    if (!empty($atts['category'])) {
        $args['category_name'] = sanitize_text_field($atts['category']);
    }

    $q = new WP_Query($args);
    if (!$q->have_posts()) return '';

    ob_start();
    ?>

    <div class="tinbmt-block">

        <?php
        $i = 0;
        while ($q->have_posts()) : $q->the_post();
            $i++;

            // =====================
            // Source logo
            // =====================
            $ref = get_post_meta(get_the_ID(), '_ref', true);
            $logos = function_exists('get_source_logos_map') ? get_source_logos_map() : [];
            $source_logo = $logos[$ref] ?? ($logos['default'] ?? '');

            // =====================
            // Time display
            // =====================
            $post_time    = get_the_time('U');
            $current_time = current_time('timestamp');
            $diff_hours   = ($current_time - $post_time) / 3600;

            $time_display = ($diff_hours <= 24)
                ? human_time_diff($post_time, $current_time) . ' trước'
                : get_the_time('d/m/Y');

            // =====================
            // Meta description
            // =====================
            $excerpt = get_the_excerpt();
            if (!$excerpt) {
                $excerpt = wp_trim_words(wp_strip_all_tags(get_the_content()), 28);
            }
        ?>

        <?php if ($i === 1): ?>
            <!-- MAIN POST -->
            <div class="tinbmt-main">

                <a href="<?php the_permalink(); ?>" class="tinbmt-main-thumb">
                    <?php the_post_thumbnail('large'); ?>
                </a>

                <h2 class="tinbmt-main-title">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h2>

                <div class="tinbmt-meta">
                    <?php if ($source_logo): ?>
                        <img src="<?php echo esc_url($source_logo); ?>" class="source-logo">
                    <?php endif; ?>
                    <span><?php echo esc_html($time_display); ?></span>
                </div>

                <div class="tinbmt-main-desc">
                    <?php echo esc_html($excerpt); ?>
                </div>

            </div>

            <div class="tinbmt-bottom">

        <?php else: ?>
            <!-- BOTTOM ITEM -->
            <div class="tinbmt-item">

                <a href="<?php the_permalink(); ?>" class="item-thumb">
                    <?php the_post_thumbnail('medium'); ?>
                </a>

                <h3 class="tinbmt-item-title">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h3>

                <div class="item-meta">
                    <?php if ($source_logo): ?>
                        <img src="<?php echo esc_url($source_logo); ?>" class="source-logo-mini">
                    <?php endif; ?>
                    <span><?php echo esc_html($time_display); ?></span>
                </div>

            </div>
        <?php endif; ?>

        <?php endwhile; ?>
            </div><!-- /.tinbmt-bottom -->

    </div>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}

add_shortcode('tinbmt_grid', 'tinbmt_grid_shortcode');
