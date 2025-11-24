<?php
// Add meta box to post editor
function my_custom_meta_box()
{
    add_meta_box(
        'my_meta_box_id',
        'Gemini AI Get Keyphrase and Slug SEO',
        'my_meta_box_callback',
        'post',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'my_custom_meta_box');

// Meta box content
function my_meta_box_callback($post)
{
?>
    <label for="my_meta_field">Enter title:</label>
    <input type="text" id="my_meta_field" name="my_meta_field" value="" style="width:100%; margin-bottom:3px" />

    <button type="button" class="button" id="my_custom_button">Get New Title SEO</button>

    <br><br>
    <label for="response_field">Response Title SEO:</label>
    <textarea id="response_field" name="response_textarea" rows="4" style="width:100%;" readonly></textarea>
<?php
}

// Enqueue JS only for post edit screen
function enqueue_custom_meta_box_script($hook)
{
    global $post;
    if ('post.php' != $hook && 'post-new.php' != $hook) {
        return;
    }
    // Enqueue jQuery (WordPress already includes jQuery, but it's good to ensure it's enqueued)
    wp_enqueue_script('jquery');

    // Enqueue the custom script
    wp_enqueue_script(
        'custom-meta-box-js',
        get_template_directory_uri() . '/short-code-custom/get-keyphrase-slug/custom-meta-box.js',
        array('jquery'), // Dependency on jQuery
        null, // Version (optional)
        true // Load in footer
    );

    // Localize the script to make AJAX URL accessible in JavaScript
    wp_localize_script('custom-meta-box-js', 'metaBoxData', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('admin_enqueue_scripts', 'enqueue_custom_meta_box_script');

/**
 * Helper: Call Gemini API
 */
function call_gemini_api($prompt)
{
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
        'sslverify' => false, // ⚠️ For development only
    ];

    $response = wp_remote_post($api_url, $args);
    $body     = wp_remote_retrieve_body($response);
    $data     = json_decode($body, true);

    return $data['candidates'][0]['content']['parts'][0]['text'] ?? "Không tìm thấy văn bản trong phản hồi.";
}

// Handle first button (Get Value)
function handle_my_custom_button_action()
{
    $meta_value = sanitize_text_field($_POST['meta_value'] ?? '');

    $prompt = $meta_value . " 
   Sử dụng tất cả công cụ phân tích từ khóa mà bạn có thể truy cập (ít nhất Google Trends, Google Keyword Planner, KeywordTool.io, Ahrefs, Semrush, Google Analytics nếu có) để chọn một title tối ưu cho bài viết trong lĩnh vực pháp lý / luật Việt Nam với các điều kiện: phạm vi tìm kiếm Việt Nam, 12 tháng gần nhất; chọn title và focus keyphrase có search volume cao nhất, ưu tiên volume thực tế (>10.000); nếu volume không đạt, báo lỗi theo định dạng riêng; nếu volume khác nhau giữa các công cụ, chọn volume cao nhất được xác nhận bởi ít nhất một trong các công cụ trả phí (Ahrefs, Semrush, Google Keyword Planner); thời gian/nguồn dữ liệu lọc nội bộ, không xuất ra kết quả.

    Chỉ in 4 dòng liên tiếp, mỗi dòng xuống 1 lần, không dòng trống, không chú thích:

    Title (câu hỏi kết thúc ?, không phải câu hỏi kết thúc ., không có dấu hai chấm)

    URL (slug chuẩn SEO, không /, chữ thường, dấu -)

    Focus Keyphrase (cụm từ chính xác, liên quan luật Việt Nam, không dấu chấm hoặc ký tự lạ)

    Search Volume (số nguyên, kèm nguồn và trend ngay trên cùng dòng, ví dụ: 15200 [Ahrefs], trend: +8%)

    Lưu ý nội bộ: nếu nhiều title có volume tương đương, chọn title có CTR tiềm năng cao hơn, intent rõ ràng cho người tìm thông tin pháp lý tại VN; ưu tiên từ khóa phổ biến theo Google Trends, xuất hiện top 10 tìm kiếm; tất cả hướng dẫn xử lý xung đột và dữ liệu nội bộ KHÔNG xuất trong output.";
    echo call_gemini_api($prompt);
    wp_die();
}
add_action('wp_ajax_my_custom_button_action', 'handle_my_custom_button_action');
