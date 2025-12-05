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
        echo $link . "\n";
    }

    // echo "Tổng số link: $totalLinks\n";

    return $epiLinks;
}

function crawl_gia_cafe_stream() {
   $original = getOriginalLinkFromEpi(); // giả sử trả về mảng các link .epi

// if (!empty($original)) {
//     // Loại bỏ các link trùng lặp
//     $original = array_unique($original);

//     // Chuyển mảng thành chuỗi, phân cách bằng dấu phẩy
//     $originalString = implode(", ", $original);

//     // In ra từng link riêng
//     $links = explode(", ", $originalString);
//     echo "Link gốc (không trùng lặp):<br>\n";
//     foreach ($links as $index => $link) {
//         echo ($index + 1) . ". <a href='$link' target='_blank'>$link</a><br>\n";
//     }
// } else {
//     echo "Không tìm được link gốc.<br>\n";
// }
}

add_shortcode('gia_cafe', 'crawl_gia_cafe_stream');
