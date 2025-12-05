<?php
function getOriginalLinkFromEpi() {
    $url = "https://baomoi.com/dak-lak-tag351.epi";
    $html = file_get_contents($url);

    $dom = new DOMDocument();
    // Suppress warnings from malformed HTML
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    // Use DOMXPath to target your specific class
    $xpath = new DOMXPath($dom);
    // Select all <a> inside elements with class "content-list" that contain .epi links
$nodes = $xpath->query(
        '//*[contains(@class,"content-list")]//h3//a[contains(@href,".epi")]'
    );
    $epiLinks = [];

    foreach ($nodes as $node) {
        $epiLinks[] = $node->getAttribute('href');
    }

    // Tổng số link
    $totalLinks = count($epiLinks);

    // echo "Found " . $totalLinks . " .epi links in content-list:\n";

    foreach ($epiLinks as $link) {
        $html = file_get_contents("https://baomoi.com".$link);
            // Tìm <script> chứa canonicalUrl
        if (preg_match_all('/originalUrl":"(https?:\/\/[^"]+)"/', $html, $matches)) {
            $originalUrl = end($matches[1]);
            $originalUrls[] = $originalUrl;
        }
    }

    // echo "Tổng số link: $totalLinks\n";

  // Loại bỏ link trùng lặp
    $originalUrls = array_unique($originalUrls);

    return $originalUrls;
}

function crawl_gia_cafe_stream() {
   $original = getOriginalLinkFromEpi(); // trả về mảng link .epi

// Nếu không có link → trả về chuỗi
if (empty($original)) {
    return "Không tìm được link gốc.<br>\n";
}

// Loại bỏ trùng
$original = array_unique($original);

// Bắt đầu build HTML để return
$output = "Link gốc (không trùng lặp):<br>\n";

foreach ($original as $index => $link) {
    $output .= ($index + 1) . ". <a href='{$link}' target='_blank'>{$link}</a><br>\n";
}

// Shortcode phải return
return $output;
}

add_shortcode('gia_cafe', 'crawl_gia_cafe_stream');
