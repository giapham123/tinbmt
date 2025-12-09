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

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $content);

    $xpath = new DOMXPath($doc);

    // ❌ REMOVE featured image injection COMPLETELY
    // (Do nothing here)

    // ✅ Beautify all <a> tags (not inside TOC)
    $links = $xpath->query('//a[not(ancestor::*[@class="toc" or @id="ez-toc-container"])]');
    foreach ($links as $link) {
        $style = $link->getAttribute('style');
        $link->setAttribute('style', trim($style . ' color: blue;'));
        $link->setAttribute('class', 'beautify-link');
        $link->setAttribute('target', '_blank');
        $link->setAttribute('rel', 'noopener noreferrer');
    }

    // ✅ Extract <body> content only
    $body = $doc->getElementsByTagName('body')->item(0);
    $newContent = '';
    foreach ($body->childNodes as $child) {
        $newContent .= $doc->saveHTML($child);
    }

    // Clean <br> or empty <p> at end
    $newContent = preg_replace('/(\s*<br\s*\/?>\s*)+$/i', '', $newContent);
    $newContent = preg_replace('/(\s*<p>\s*<\/p>\s*)+$/i', '', $newContent);

    // Add post tags at bottom
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

    // ✅ Add title at the top (centered & bigger)
    $title_html = '<h1 class="beautify-post-title" style="text-align: center; font-size: 2em; margin-bottom: 10px;">' . esc_html($post_title) . '</h1>';

    return '<div class="beautify-post">' . $title_html . trim($newContent) . '</div>';
}

add_shortcode('post_content', 'shortcode_post_content');
