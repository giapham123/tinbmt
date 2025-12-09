<?php
// Add meta box to post editor
function my_custom_slug_keyphrase()
{
    add_meta_box(
        'parse_slug_keyphrase_box',      // Unique ID
        'Parse Slug and Keyphrase',      // Box title
        'parse_slug_keyphrase_box_callback',
        'post',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'my_custom_slug_keyphrase');

// Meta box content
function parse_slug_keyphrase_box_callback($post)
{
?>
    <label>Enter JSON Content:</label>
    <textarea id="my_meta_field" style="width:100%; margin-bottom:6px" rows="4"></textarea>

    <button type="button" class="button button-primary" id="parse_json_button">
        Parse JSON
    </button>

    <br><br>

    <label>Result</label>
    <textarea id="response_field_parse_json" rows="4" style="width:100%;"></textarea>
<?php
}

// Enqueue JS
function gks_parse_enqueue_scripts($hook)
{
    // Only load on add/edit post
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }

    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'my_custom_slug_keyphrase_js',
        get_template_directory_uri() . '/short-code-custom/get-keyphrase-slug-parse-json/get-keyphrase-slug-parse-json.js',
        array('jquery'), // Dependency on jQuery
        null, // Version (optional)
        true // Load in footer
    );

    wp_localize_script('my_custom_slug_keyphrase_js', 'metaBoxData', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);
}
add_action('admin_enqueue_scripts', 'gks_parse_enqueue_scripts');


function gks_handle_button_action()
{
    // Set header để hiển thị tiếng Việt đúng
    header('Content-Type: application/json; charset=UTF-8');

    $jsonContent = stripslashes($_POST['meta_value'] ?? '');

    // Decode JSON với UTF-8
    $data = json_decode($jsonContent, true);

    if (!$data) {
        echo json_encode(['error' => 'Invalid JSON'], JSON_UNESCAPED_UNICODE);
        wp_die();
    }

    echo json_encode([
        'title' => $data['title'] ?? '',
        'slug'  => $data['slug'] ?? '',
        'focus' => $data['focus_keyphrase'] ?? ''
    ], JSON_UNESCAPED_UNICODE);

    wp_die();
}

add_action('wp_ajax_gks_handle_button_action', 'gks_handle_button_action');
