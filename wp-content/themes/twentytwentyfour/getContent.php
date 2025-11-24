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

    // ✅ Add featured image under <h1> or at the top if no <h1>
    $featured_img_url = get_the_post_thumbnail_url($post_id, 'full');
    if ($featured_img_url) {
        $img = $doc->createElement('img');
        $img->setAttribute('src', esc_url($featured_img_url));
        $img->setAttribute('alt', esc_attr($post_title));
        $img->setAttribute('class', 'beautify-featured-image');
        $img->setAttribute('style', 'display:block;margin:15px auto;max-width:100%;height:auto;max-height:350px;object-fit:cover;border-radius:10px;');

        $h1 = $xpath->query('//h1')->item(0);
        if ($h1) {
            // Insert image right after <h1>
            if ($h1->nextSibling) {
                $h1->parentNode->insertBefore($img, $h1->nextSibling);
            } else {
                $h1->parentNode->appendChild($img);
            }
        } else {
            // If no <h1> found, insert image at the top
            $body = $doc->getElementsByTagName('body')->item(0);
            if ($body->firstChild) {
                $body->insertBefore($img, $body->firstChild);
            } else {
                $body->appendChild($img);
            }
        }
    }

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

    // ✅ Clean up extra <br> or empty <p> at the end
    $newContent = preg_replace('/(\s*<br\s*\/?>\s*)+$/i', '', $newContent);
    $newContent = preg_replace('/(\s*<p>\s*<\/p>\s*)+$/i', '', $newContent);

    // ✅ Add post tags at the bottom
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

    return '<div class="beautify-post">' . trim($newContent) . '</div>';
}

// Register shortcode
add_shortcode('post_content', 'shortcode_post_content');
