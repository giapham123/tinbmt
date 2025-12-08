<?php
// Register the custom meta box
function custom_meta_box() {
    add_meta_box(
        'Parse Slug and Keyphrase from JSON',          // Unique ID
        'Parse Slug and Keyphrase',             // Box title
        'parse_slug_keyphrase_box',        // Content callback
        'post',                        // Post type (can be 'page', 'post', or custom post type)
        'normal',                      // Context (normal, side, advanced)
        'high'                         // Priority (default, low, high)
    );
}
add_action('add_meta_boxes', 'custom_meta_box');

// HTML for the custom meta box
function parse_slug_keyphrase_box($post) {
    ?>
    <label for="input_type">Choose Input Type:</label>
    <select id="input_type" style="width: 100%; margin-bottom: 10px;">
        <option value="link">Input Link</option>
        <option value="content">Input Content</option>
    </select>

    <!-- Input Link Section -->
    <div id="link_section">
        <textarea id="input_link" name="input_link" rows="2" style="width: 100%"></textarea>
        <button type="button" id="handle_link" class="button">Handle Link</button>
    </div>

    <!-- Input Content Section -->
    <div id="content_section" style="display:none;">
        <textarea id="custom_link" name="custom_link" rows="5" style="width: 100%"></textarea>
        <button type="button" id="handle_link_button" class="button">Handle Content</button>
    </div>

    <br><br>

    <label for="custom_response">Response:</label>
    <textarea id="custom_response" name="custom_response" rows="20" style="width: 100%"></textarea>
    <script>
        const inputType = document.getElementById('input_type');
        const linkSection = document.getElementById('link_section');
        const contentSection = document.getElementById('content_section');

        inputType.addEventListener('change', function() {
            if(this.value === 'link') {
                linkSection.style.display = 'block';
                contentSection.style.display = 'none';
            } else {
                linkSection.style.display = 'none';
                contentSection.style.display = 'block';
            }
        });
    </script>
    <?php
}

// Enqueue the JavaScript for handling the button click
function enqueue_custom_get_post_via_link_script($hook) {
    // Only load on the post and page edit screens
    if ('post.php' != $hook && 'post-new.php' != $hook) {
        return;
    }

    // Enqueue jQuery (WordPress already includes jQuery, but it's good to ensure it's enqueued)
    wp_enqueue_script('jquery');

    // Enqueue the custom script
    wp_enqueue_script(
        'get-post-link-js', // Handle for the script
        get_template_directory_uri() . '/short-code-custom/get-post-via-link/get-post-link.js', // Path to the JS file
        array('jquery'), // Dependency on jQuery
        null, // Version (optional)
        true // Load in footer
    );

    // Localize the script to make AJAX URL accessible in JavaScript
    wp_localize_script('get-post-link-js', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php') // Pass the AJAX URL to JavaScript
    ));
}
add_action('admin_enqueue_scripts', 'enqueue_custom_get_post_via_link_script');

// Handle the AJAX request (For logged-in users)
function handle_get_post_link() {
    // Get the link from the request
    $link = isset($_POST['link']) ? sanitize_text_field($_POST['link']) : '';
    $prompt = $link."
Nhiệm vụ: Truy cập vào bài viết tại [LIÊN KẾT] và thực hiện các yêu cầu sau:

Giữ lại phần nội dung chính (body):

Chỉ lấy nội dung nằm trong thẻ <body> của bài viết.

Giữ nguyên cấu trúc gốc: tiêu đề, mục lục, đoạn văn, tiêu đề phụ, danh sách, bảng biểu, v.v.

Trích xuất phần “Căn cứ pháp lý” (nếu có):

Giữ nguyên nội dung văn bản gốc của các điều luật, bộ luật.

Không thêm hoặc chèn bất kỳ hyperlink, href hay liên kết nào.

Định dạng HTML chuẩn:

Sử dụng thẻ <h1>, <h2>, <h3> cho tiêu đề và tiêu đề phụ.

Loại bỏ toàn bộ ký hiệu * hoặc ** trong nội dung.

Làm nổi bật các thông tin quan trọng bằng <strong> hoặc <em>.

Không sử dụng thẻ <blockquote> trong bất kỳ phần nào.

Giữ nguyên nội dung, trình bày rõ ràng, chia thành các ý chính để dễ đọc.

Tối ưu SEO theo chuẩn Google:

Thêm meta description ngắn gọn (150–160 ký tự), hấp dẫn, liên quan trực tiếp đến title và nội dung bài viết.

Thêm từ khóa chính (focus keyphrase) và từ khóa phụ liên quan đến chủ đề.

Đảm bảo cấu trúc HTML thân thiện SEO, rõ ràng, dễ thu thập dữ liệu.

Không chèn thẻ <style> trong <head> hoặc bất kỳ phần CSS nào.

Yêu cầu đầu ra:

Kết quả là nội dung HTML hoàn chỉnh, sạch, chuẩn SEO, giữ nguyên cấu trúc và thông tin gốc.

Không chứa bất kỳ hyperlink, href, liên kết ngoài hoặc thẻ <style> nào.

Meta description phải hấp dẫn, đúng chuẩn SEO, liên quan trực tiếp đến title và nội dung bài viết.

    ";
   $api_key  = 'AIzaSyCF6tM5e-AqFUGMV-5KNFlc0vEVydkjNwM';
    $model_id = 'gemini-2.0-flash';
    $api_url  = "https://generativelanguage.googleapis.com/v1beta/models/{$model_id}:generateContent?key={$api_key}";

    $body = json_encode([
        'contents' => [[
            'parts' => [['text' => $prompt]]
        ]]
    ]);

    $args = [
        'method'    => 'POST',
        'headers'   => ['Content-Type' => 'application/json'],
        'body'      => $body,
        'timeout'   => 45,
        'sslverify' => false, // ⚠️ For development only, avoid in production
    ];

    $response = wp_remote_post($api_url, $args);
    $body     = wp_remote_retrieve_body($response);
    $data     = json_decode($body, true);

    echo $data['candidates'][0]['content']['parts'][0]['text'] ?? "Không tìm thấy văn bản trong phản hồi.";
    wp_die(); // Always call this to terminate AJAX processing
}

function handle_link_button()
{
    // Get the link from the request
    $link = isset($_POST['link']) ? sanitize_text_field($_POST['link']) : '';
    $url = $link;

    // Giả lập trình duyệt để bypass block
    $context = stream_context_create([
        "http" => [
            "method" => "GET",
            "header" =>
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
                . "(KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36\r\n"
                . "Accept-Language: en-US,en;q=0.9\r\n"
        ]
    ]);

    // Tải HTML
    $html = @file_get_contents($url, false, $context);
    if ($html === false) {
        die("Không thể tải trang.");
    }

    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

    // Parse DOM
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    // ✅ Tìm đúng phần article content
    $nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' news-content ')]");
    log($nodes->length);
    if ($nodes->length === 0) {
        die("Không tìm thấy phần tử class='news-content'");
    }

    $node = $nodes->item(0);

    // ✅ Lấy HTML bên trong
    $innerHtml = '';
    foreach ($node->childNodes as $child) {
        $innerHtml .= $dom->saveHTML($child);
    }
    $prompt = $innerHtml . "
    Giữ lại phần nội dung chính (<body>) từ bài viết tại [LIÊN KẾT].

Trích xuất phần “Căn cứ pháp lý” (nếu có):

Giữ nguyên nội dung gốc của các điều luật, bộ luật.

Thêm hyperlink hợp lệ trực tiếp đến văn bản luật tại các nguồn chính thống của nhà nước (không dùng href='#', không link tới luatvietnam.vn hoặc thuvienphapluat.vn).

Cấu trúc và định dạng HTML chuẩn:

Giữ nguyên cấu trúc gốc: tiêu đề, tiêu đề phụ, đoạn văn, mục lục, danh sách, bảng biểu, v.v.

Sử dụng thẻ <h1>, <h2>, <h3> cho tiêu đề và tiêu đề phụ.

Loại bỏ toàn bộ ký hiệu * hoặc **.

Chia nội dung thành các ý chính, làm nổi bật thông tin quan trọng bằng <strong> hoặc <em>.

Không sử dụng thẻ <blockquote> trong bất kỳ phần nào.

Chỉ lấy nội dung bên trong thẻ <body>.

SEO chuẩn Google:

Thêm thẻ title, meta description hấp dẫn và liên quan trực tiếp đến nội dung bài, và keywords liên quan.

Bổ sung các hyperlink hợp lệ đến các trang uy tín liên quan (ví dụ: trang chính phủ, bộ luật, văn bản pháp luật).

Đảm bảo cấu trúc HTML thân thiện SEO, dễ thu thập dữ liệu.

Thêm từ khóa phụ liên quan đến chủ đề bài viết.

Yêu cầu đầu ra:

Kết quả là HTML hoàn chỉnh, sạch, chuẩn SEO, giữ nguyên cấu trúc gốc và tất cả các yếu tố quan trọng.

Nội dung chi tiết, đầy đủ, không bỏ sót các phần quan trọng.

Các hyperlink đến văn bản pháp luật phải hợp lệ và đáng tin cậy.
    ";
    $api_key  = 'AIzaSyCF6tM5e-AqFUGMV-5KNFlc0vEVydkjNwM';
    $model_id = 'gemini-2.0-flash';
    $api_url  = "https://generativelanguage.googleapis.com/v1beta/models/{$model_id}:generateContent?key={$api_key}";

    $body = json_encode([
        'contents' => [[
            'parts' => [['text' => $prompt]]
        ]]
    ]);

    $args = [
        'method'    => 'POST',
        'headers'   => ['Content-Type' => 'application/json'],
        'body'      => $body,
        'timeout'   => 45,
        'sslverify' => false, // ⚠️ For development only, avoid in production
    ];

    $response = wp_remote_post($api_url, $args);
    $body     = wp_remote_retrieve_body($response);
    $data     = json_decode($body, true);

    echo $data['candidates'][0]['content']['parts'][0]['text'] ?? "Không tìm thấy văn bản trong phản hồi.";
    wp_die(); // Always call this to terminate AJAX processing
}

// Hook the AJAX action for logged-in users
add_action('wp_ajax_handle_get_post_link', 'handle_get_post_link'); 

// For non-logged-in users (if you want to handle public users as well)
add_action('wp_ajax_nopriv_handle_get_post_link', 'handle_get_post_link');

add_action('wp_ajax_handle_link_button', 'handle_link_button');
add_action('wp_ajax_nopriv_handle_link_button', 'handle_link_button');

?>
