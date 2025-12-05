<?php
function custom_latest_posts_small_shortcode() {

    remove_filter('the_content', 'wpautop');
    remove_filter('the_content', 'wptexturize');
    remove_filter('the_content', 'shortcode_unautop');

    ob_start();

    $args = [
        "post_type"      => "post",
        "posts_per_page" => 5,
        "orderby"        => "date",
        "order"          => "DESC",
    ];

    $q = new WP_Query($args);
    if (!$q->have_posts()) return "";

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

    while ($q->have_posts()) {
        $q->the_post();

        // Try to get FULL SIZE featured image
        $full_thumb = get_the_post_thumbnail_url(get_the_ID(), "full");

        // If no featured image → get first image from content
        if (!$full_thumb) {
            $content = get_the_content();
            preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches);
            $full_thumb = !empty($matches[1]) ? $matches[1] : "";
        }

        // Output medium version if featured image exists
        $medium_thumb = get_the_post_thumbnail_url(get_the_ID(), "medium");

        // If medium exists → use medium, else use full/content image
        $display_img = $medium_thumb ?: $full_thumb;

        $title = get_the_title();
        $link  = get_permalink();
        $date  = get_the_date("d/m/Y");
        $cats  = get_the_category();
        $cat_name = (!empty($cats)) ? $cats[0]->name : "Tin mới";

        $justify = "text-align:justify; text-justify:inter-word;";
        ?>

        <?php if ($count === 0): ?>

            <div style="margin-bottom:14px;">

                <?php if ($display_img): ?>
                    <img src="<?php echo esc_url($display_img); ?>" 
                         style="width:100%; border-radius:6px; margin-bottom:8px;">
                <?php endif; ?>

                <a href="<?php echo esc_url($link); ?>" 
                   style="font-size:18px; font-weight:700; line-height:1.35; text-decoration:none; color:#000; display:block; <?php echo $justify; ?>">
                   <?php echo esc_html($title); ?>
                </a>

                <div style="color:#777; font-size:12px; margin:4px 0 8px 0;">
                    <?php echo esc_html($cat_name); ?> • <?php echo esc_html($date); ?>
                </div>

                <div style="font-size:14px; line-height:1.45; color:#333;">
                    <?php echo esc_html(wp_trim_words(get_the_excerpt(), 30)); ?>
                </div>

            </div>

            <hr style="border:0; border-top:1px solid #eee; margin:10px 0;">

        <?php else: ?>

            <a href="<?php echo esc_url($link); ?>"
            style="display:flex; gap:10px; margin-bottom:0px; text-decoration:none; padding:6px 0; color:#000;">

                <?php if ($display_img): ?>
                    <img src="<?php echo esc_url($display_img); ?>" 
                        style="width:90px; height:100%; object-fit:cover; border-radius:4px; flex-shrink:0;">
                <?php endif; ?>

                <div style="flex:1; display:flex; flex-direction:column; justify-content:center;">
                    <div style="font-size:15px; font-weight:600; line-height:1.35; color:#000;">
                        <?php echo esc_html($title); ?>
                    </div>

                    <div style="margin-top:4px; color:#666; font-size:12px;">
                        <?php echo esc_html($cat_name); ?> • <?php echo esc_html($date); ?>
                    </div>
                </div>

            </a>

            <?php if ($count < $q->post_count - 1): ?>
                <hr style="border:0; border-top:1px solid #eee; margin:10px 0;">
            <?php endif; ?>

        <?php endif; ?>

        <?php $count++; ?>

    <?php } ?>

    </div>

    <?php
    wp_reset_postdata();

    $html = ob_get_clean();
    $html = preg_replace('/<p>\s*<\/p>/i', '', $html);
    $html = str_replace(["\r", "\n"], "", $html);

    return $html;
}

add_shortcode("latest_post", "custom_latest_posts_small_shortcode");
