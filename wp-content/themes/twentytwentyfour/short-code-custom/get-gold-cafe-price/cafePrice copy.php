<?php
function crawl_gia_cafe_stream() {

    $context = stream_context_create([
        "http" => [
            "header" => 
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n" .
                "Accept-Encoding: gzip, deflate\r\n"
        ]
    ]);

    $url = "https://nhandan.vn/video-nguoi-dan-dak-lak-thieu-nuoc-sach-sau-lu-lon-post925891.html";
    $html = file_get_contents($url, false, $context);

    if (!$html) return "Không tải được dữ liệu.";

    // Nếu là gzip → giải nén
    if (substr($html, 0, 2) === "\x1f\x8b") {
        $html = gzdecode($html);
    }

    // Debug thử xem có đẹp chưa
    error_log($html);

    // Parse DOM
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query("//*[contains(@class,'main-col') and contains(@class,'content-col')]");

    if ($nodes->length === 0) {
        return "Không tìm thấy class main-col content-col.";
    }

    // Lấy HTML của node đầu tiên
    $content = $dom->saveHTML($nodes->item(0));

    return $content;
}

add_shortcode('gia_cafe', 'crawl_gia_cafe_stream');
