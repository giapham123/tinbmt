<?php
function shortcode_related_category_full_desc() {

    if (!is_single()) return "";

    $categories = get_the_category();
    if (empty($categories)) return "";

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
    if (!$q->have_posts()) return "";

    ob_start();
    ?>

    <style>
        .related-flex {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
            width: 100%;
            flex-wrap: nowrap;
        }

        .related-img-box {
            width: 200px;
            height: auto;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .related-img-box img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            max-height: 150px;
            border-radius: 6px;
        }

        .related-text {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }

        .related-title {
            word-break: break-word;
            white-space: normal;
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
        }
    </style>

    <div style="margin-top:8px;">

    <?php while ($q->have_posts()): $q->the_post();

        $thumb = get_the_post_thumbnail_url(get_the_ID(), "medium");
        $title = get_the_title();
        $link  = get_permalink();
        $date  = get_the_date("d/m/Y");

        $cats = get_the_category();
        $cat_name = !empty($cats) ? $cats[0]->name : "Tin mới";

        $content_raw = get_the_content();
        $content_clean = wp_strip_all_tags($content_raw);
        $content_clean = preg_replace('/\s+/u', ' ', $content_clean);
        $full_desc = wp_trim_words($content_clean, 80, "...");
    ?>

        <div class="related-flex" style="margin-bottom:10px;">
            <div class="related-img-box">
                <a href="<?php echo esc_url($link); ?>">
                    <img src="<?php echo esc_url($thumb); ?>">
                </a>
            </div>

            <div class="related-text">
                <div class="related-title">
                    <a style="font-size:15px;font-weight:600;line-height:1.3;text-decoration:none;color:#000;"
                       href="<?php echo esc_url($link); ?>">
                        <?php echo esc_html($title); ?>
                    </a>
                </div>

                <div class="related-meta" style="color:#777;font-size:12px;margin:2px 0;">
                    <?php echo esc_html($cat_name); ?> • <?php echo esc_html($date); ?>
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

    // Clean <p></p> issues
    $html = ob_get_clean();
    $html = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $html);
    $html = str_replace(["\n", "\r"], '', $html);

    return $html;
}

add_shortcode("related_category", "shortcode_related_category_full_desc");
