<?php
function shortcode_related_category_full_desc() {
    if (!is_single()) {
        return "";
    }

    $categories = get_the_category();
    if (empty($categories)) {
        return "";
    }

    $cat_id = $categories[0]->term_id;

    $args = [
        "post_type"      => "post",
        "posts_per_page" => 3,
        "post__not_in"   => [get_the_ID()],
        "cat"            => $cat_id,
        "orderby"        => "date",
        "order"          => "DESC",
    ];

    $q = new WP_Query($args);
    if (!$q->have_posts()) {
        return "";
    }

    ob_start();
    ?>

    <style>
        .related-flex {
            display: flex;
            align-items: flex-start; /* IMAGE STAYS TOP */
            gap: 10px;
            width: 100%;
        }

        /* IMAGE – TOP BLOCK */
        .related-img-box {
            width: 200px;
            flex-shrink: 0;
        }

        .related-img-box img {
            width: 100%;
            height: auto;
            max-height: 150px;
            object-fit: contain;
            display: block;
        }

        /* TEXT – CENTERED RELATIVE TO IMAGE */
        .related-text {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center; /* TEXT MIDDLE */
            min-height: 150px; /* MATCH IMAGE HEIGHT */
        }

        .related-title {
            word-break: break-word;
        }

        @media (max-width: 768px) {
            .related-desc {
                display: none !important;
            }

            .related-img-box {
                width: 120px;
            }

            .related-img-box img {
                max-height: 120px;
            }

            .related-text {
                min-height: 120px;
            }
        }
    </style>

    <div style="margin-top:8px;">

    <?php while ($q->have_posts()) : $q->the_post(); ?>

        <?php
        // Source logo
        $ref = get_post_meta(get_the_ID(), '_ref', true);
        $logo_map = function_exists('get_source_logos_map') ? get_source_logos_map() : [];
        $source_logo = $logo_map[$ref] ?? '';

        // Time
        $post_time    = get_the_time('U');
        $current_time = current_time('timestamp');
        $diff_hours   = ($current_time - $post_time) / 3600;

        if ($diff_hours <= 24) {
            $time_display = human_time_diff($post_time, $current_time) . " trước";
        } else {
            $time_display = get_the_time("d/m/Y");
        }

        // Data
        $thumb    = get_the_post_thumbnail_url(get_the_ID(), "medium");
        $title    = get_the_title();
        $link     = get_permalink();
        $cats     = get_the_category();
        $cat_name = $cats ? $cats[0]->name : "Tin mới";

        $content_clean = wp_strip_all_tags(get_the_content());
        $content_clean = preg_replace('/\s+/u', ' ', $content_clean);
        $full_desc     = wp_trim_words($content_clean, 80, "...");
        ?>

        <div class="related-flex" style="margin-bottom:10px;">

            <div class="related-img-box">
                <a href="<?php echo esc_url($link); ?>">
                    <?php if ($thumb) : ?>
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>">
                    <?php endif; ?>
                </a>
            </div>

            <div class="related-text">

                <div class="related-title">
                    <a href="<?php echo esc_url($link); ?>"
                       style="font-size:15px;font-weight:600;line-height:1.3;text-decoration:none;color:#000;">
                        <?php echo esc_html($title); ?>
                    </a>
                </div>

                <div style="color:#777;font-size:12px;margin:2px 0;white-space:nowrap;">
                    <?php if ($source_logo) : ?>
                        <img src="<?php echo esc_url($source_logo); ?>"
                             style="height:14px;vertical-align:middle;">
                        &nbsp;
                    <?php endif; ?>
                    <span><?php echo esc_html($time_display); ?></span> •
                    <span><?php echo esc_html($cat_name); ?></span>
                </div>

                <div class="related-desc" style="font-size:14px;color:#333;line-height:1.4;margin-top:4px;">
                    <?php echo esc_html($full_desc); ?>
                </div>

            </div>
        </div>

    <?php endwhile; ?>

    </div>

    <?php
    wp_reset_postdata();

    $html = ob_get_clean();
    $html = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $html);
    $html = str_replace(["\n", "\r"], '', $html);

    return $html;
}

add_shortcode("related_category", "shortcode_related_category_full_desc");
