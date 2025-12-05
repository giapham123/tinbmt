<?php
function getOriginalLinkFromEpi(string $epiUrl, int $maxRetries = 5, int $waitSeconds = 1): ?string {
  $url = "https://baomoi.com/cong-an-phuong-hoan-kiem-trao-tra-tai-san-danh-roi-cho-nguoi-dan-c53862379.epi";
    $html = file_get_contents($url);
    // Tìm <script> chứa canonicalUrl
    if (preg_match_all('/originalUrl":"(https?:\/\/[^"]+)"/', $html, $matches)) {
    $originalUrl = end($matches[1]);
        echo "Link gốc: " . $originalUrl;
    } else {
        echo "Không tìm thấy link gốc.";
    }
    return null;
}

function crawl_gia_cafe_stream() {
    $epi = 'https://baomoi.com/cong-an-phuong-hoan-kiem-trao-tra-tai-san-danh-roi-cho-nguoi-dan-c53862379.epi';
    $original = getOriginalLinkFromEpi($epi, 5, 1); // retry 5 lần, mỗi lần chờ 1 giây

    if ($original) {
        return "Link gốc: $original\n";
    } else {
        return "Không tìm được link gốc.\n";
    }
}

add_shortcode('gia_cafe', 'crawl_gia_cafe_stream');


// // $epi = "https://baomoi.com/bsr-huong-ve-vung-lu-dak-lak-nhung-chuyen-hang-am-ap-tinh-nguoi-c53863972.epi";

// // $original = getOriginalLinkFromBaomoi($epi);

// error_log ($original);