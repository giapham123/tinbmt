<?php
// Register the custom meta box
function parse_content_meta_box() {
    add_meta_box(
        'parse_content_from_json',          // Unique ID
        'Parse Content From JSON',             // Box title
        'parse_content_meta_box_html',        // Content callback
        'post',                        // Post type (can be 'page', 'post', or custom post type)
        'normal',                      // Context (normal, side, advanced)
        'high'                         // Priority (default, low, high)
    );
}
add_action('add_meta_boxes', 'parse_content_meta_box');

// HTML for the custom meta box
function parse_content_meta_box_html($post) {
    ?>
    <textarea id="input_content" name="input_content" rows="2" style="width: 100%"></textarea>
    <button type="button" id="handle_button_parse_content" class="button">Handle Link</button>

    <br><br>

    <label for="custom_response">Response:</label>
    <textarea id="custom_response" name="custom_response" rows="20" style="width: 100%"></textarea>
    <?php
}

function enqueue_parse_content_script($hook) {

    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'get-post-via-json-content-js',
        get_template_directory_uri() . '/short-code-custom/get-post-via-json-content/get-post-via-json-content.js',
        array('jquery'),
        null,
        true
    );

    wp_localize_script('get-post-via-json-content-js', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('admin_enqueue_scripts', 'enqueue_parse_content_script');
    function handle_link_button_parse_content() {
        error_log('[AJAX] handle_link_button_parse_content called');

        // Lấy dữ liệu JSON từ AJAX
        $jsonContent = isset($_POST['content']) ? wp_unslash($_POST['content']) : '';

        error_log('[AJAX] Received JSON: ' . $jsonContent);

        // Giải mã JSON về mảng
        $data = json_decode($jsonContent, true);

        // Nếu JSON lỗi → trả lỗi
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            wp_send_json_error([
                'message' => 'Invalid JSON format',
                'error'   => json_last_error_msg()
            ]);
        }

        // Chuẩn bị dữ liệu trả về
        $response = [
            'body'           => $data['body'] ?? '',
            'meta'           => $data['meta'] ?? '',
            'tags'           => $data['tags'] ?? '',
            // 'title'          => sanitize_text_field($data['title'] ?? ''),
            // 'slug'           => sanitize_title($data['slug'] ?? ''),
            // 'focus_keyphrase'=> sanitize_text_field($data['focus_keyphrase'] ?? '')
        ];

        wp_send_json_success($response);
    }
    add_action('wp_ajax_handle_link_button_parse_content', 'handle_link_button_parse_content');




    