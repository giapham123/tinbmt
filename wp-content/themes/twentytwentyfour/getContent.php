<?php
function shortcode_post_content($atts) {
    global $post;

    $atts = shortcode_atts(
        array('id' => 0),
        $atts,
        'post_content'
    );

    $post_id = $atts['id'] ? intval($atts['id']) : $post->ID;
    if (!$post_id) return '⚠️ No post found';

    $content = get_post_field('post_content', $post_id);
    if (!$content) return '⚠️ Post not found';

    $content = apply_filters('the_content', $content);
    $post_title = get_the_title($post_id);

    // ===================================================
    // TIME DISPLAY (NEW)
    // ===================================================
    $post_time    = get_post_time('U', true, $post_id);
    $current_time = current_time('timestamp');
    $diff_hours   = ($current_time - $post_time) / 3600;

    if ($diff_hours <= 24) {
        $time_display = human_time_diff($post_time, $current_time) . " trước";
    } else {
        $time_display = get_the_time("d/m/Y", $post_id);
    }

    // ============================
    //  GET REF + LOGO
    // ============================
    $ref = get_post_meta($post_id, '_ref', true);
    $logos = get_source_logos_map();
    $logo = $logos[$ref] ?? ($logos['default'] ?? '');

    // Build logo block (if exists)
    $meta_block = '';
    if (!empty($logo)) {
     $meta_block = '
    <div class="beautify-meta-block"
         style="
            display:flex;
            align-items:center;
            gap:6px;
            margin:0;
            padding:0;
            text-align:left;
            font-size:13px;
            color:#666;
        ">
        <img src="' . esc_url($logo) . '"
             style="
                height:18px;
                width:auto;
                display:block;
                object-fit:contain;
                margin:0;
                padding:0;
             ">
        <span style="margin:0; padding:0;">' . esc_html($time_display) . '</span>
    </div>';
    }

    // ============================
    // Process HTML to beautify links
    // ============================
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $content);
    $xpath = new DOMXPath($doc);

    // Beautify <a> (except inside TOC)
    $links = $xpath->query('//a[not(ancestor::*[@class="toc" or @id="ez-toc-container"])]');
    foreach ($links as $link) {
        $style = $link->getAttribute('style');
        $link->setAttribute('style', trim($style . ' color: blue;'));
        $link->setAttribute('class', 'beautify-link');
        $link->setAttribute('target', '_blank');
        $link->setAttribute('rel', 'noopener noreferrer');
    }

    // Extract content from <body>
    $body = $doc->getElementsByTagName('body')->item(0);
    $newContent = '';
    foreach ($body->childNodes as $child) {
        $newContent .= $doc->saveHTML($child);
    }

    // Clean trailing tags
    $newContent = preg_replace('/(\s*<br\s*\/?>\s*)+$/i', '', $newContent);
    $newContent = preg_replace('/(\s*<p>\s*<\/p>\s*)+$/i', '', $newContent);

    // Add TAGS
    $tags_list = get_the_tags($post_id);
    if ($tags_list) {
        $newContent .= '<div class="beautify-tags"><strong>📌 Tags:</strong> ';
        foreach ($tags_list as $tag) {
            $tag_link = get_tag_link($tag->term_id);
            $tag_name = esc_html($tag->name);
            if (strpos($tag_name, '#') !== 0) {
                $tag_name = '#' . $tag_name;
            }
            $newContent .= '<a href="' . esc_url($tag_link) . '" class="beautify-tag" target="_blank" rel="noopener noreferrer">' . $tag_name . '</a> ';
        }
        $newContent .= '</div>';
    }

    // ============================
    // FINAL HTML OUTPUT
    // ============================
    $title_html = '
        <h1 class="beautify-post-title" 
            style="text-align: center; font-size: 2em; margin-bottom: 5px;">
            ' . esc_html($post_title) . '
        </h1>';

    return '<div class="beautify-post">' 
            . $title_html 
            . $meta_block   // 👈 LOGO + TIME HERE
            . trim($newContent) 
        . '</div>';
}

add_shortcode('post_content', 'shortcode_post_content');
