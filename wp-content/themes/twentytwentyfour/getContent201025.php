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

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $content);

    $xpath = new DOMXPath($doc);
    // Get <a> tags NOT inside TOC
    $links = $xpath->query('//a[not(ancestor::*[@class="toc" or @id="ez-toc-container"])]');

    foreach ($links as $link) {
        $style = $link->getAttribute('style');
        $link->setAttribute('style', trim($style . ' color: blue;'));
        $link->setAttribute('class', 'beautify-link');
        
        // ✅ Force open in new tab
        $link->setAttribute('target', '_blank');
        $link->setAttribute('rel', 'noopener noreferrer');
    }

    // Extract body only
    $body = $doc->getElementsByTagName('body')->item(0);
    $newContent = '';
    foreach ($body->childNodes as $child) {
        $newContent .= $doc->saveHTML($child);
    }

    // ✅ Remove extra <br> or whitespace at the end
    $newContent = preg_replace('/(\s*<br\s*\/?>\s*)+$/i', '', $newContent);
    $newContent = preg_replace('/(\s*<p>\s*<\/p>\s*)+$/i', '', $newContent);

    // ✅ Add Post Tags at the bottom
    $tags_list = get_the_tags($post_id);
    if ($tags_list) {
        $newContent .= '<div class="beautify-tags"><strong>📌 Tags:</strong> ';
        foreach ($tags_list as $tag) {
            $tag_link = get_tag_link($tag->term_id);

            // Check if tag name already starts with "#"
            $tag_name = esc_html($tag->name);
            if (strpos($tag_name, '#') !== 0) {
                $tag_name = '#' . $tag_name;
            }

            $newContent .= '<a href="' . esc_url($tag_link) . '" class="beautify-tag" target="_blank" rel="noopener noreferrer">' . $tag_name . '</a> ';
        }
        $newContent .= '</div>';
    }

    return '<div class="beautify-post">' . trim($newContent) . '</div>';
}
