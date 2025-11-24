<?php


require_once plugin_dir_path(__FILE__) . 'gdrive-helpers.php';
function rpt_enqueue_admin_scripts($hook)
{
    if ('post.php' === $hook || 'post-new.php' === $hook) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('rpt-script', plugins_url('script.js', __FILE__), array('jquery'), null, true);
        wp_localize_script('rpt-script', 'rpt_ajax_obj', array('ajax_url' => admin_url('admin-ajax.php')));
        wp_enqueue_style('rpt-style', plugins_url('script.css', __FILE__)); // Enqueue the CSS file
    }
}
add_action('admin_enqueue_scripts', 'rpt_enqueue_admin_scripts');

function rpt_add_metabox()
{
    add_meta_box(
        'rpt_title_metabox',
        'Post Facebook Page',
        'rpt_metabox_callback',
        'post',
        'side'
    );
}
add_action('add_meta_boxes', 'rpt_add_metabox');

function rpt_metabox_callback()
{
    echo '<button id="get-title-btn">Get All Infor</button>';
    echo '<button id="send-api-btn" style="margin-left: 10px;">Post Facebook</button>'; // New button for sending email
    echo '<textarea id="api-response" rows="5" cols="30" style="width: 100%; margin-top: 10px;" placeholder="Content for post FB..."></textarea>'; // Text area for API response
    echo '<input type="text" id="image-url" style="width: 100%; margin-top: 10px;">';
    echo '<textarea id="link-url" rows="5" cols="30" style="width: 100%; margin-top: 10px;"></textarea>';
    // echo '<div id="loading-popup" style="display:none; padding:20px; background:#fff; border:1px solid #ccc; z-index:9999; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); width:300px; box-shadow:0 2px 10px rgba(0,0,0,0.5);"><h3>Loading...</h3></div>';
    // echo '<div id="title-popup" style="display:none; padding:20px; background:#fff; border:1px solid #ccc; z-index:9999; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); width:300px; box-shadow:0 2px 10px rgba(0,0,0,0.5);"><h3></h3><div id="post-title"></div><button id="close-popup" style="margin-top:10px;">Close</button></div>';
    echo '<div id="loading-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9998;"></div>';
    echo '<div id="loading-popup" style="display:none; padding:20px; background:#fff; border:1px solid #ccc; z-index:9999; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); width:300px; box-shadow:0 2px 10px rgba(0,0,0,0.5);"><div class="loader"></div><h3 style="text-align:center;">Loading...</h3></div>';
    echo '<div id="title-popup" style="display:none; padding:20px; background:#fff; border:1px solid #ccc; z-index:9999; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); width:300px; box-shadow:0 2px 10px rgba(0,0,0,0.5);"><h3></h3><div id="post-title"></div><button id="close-popup" style="margin-top:10px;">Close</button></div>';
}

function rpt_get_title()
{
    $post_id = intval($_POST['post_id']);
    $prompt = get_post_field('post_content', $post_id) . " .dựa vào content bài viết, viết cho tôi đoạn ngắn post facebook và thêm emojis, không quá 200 từ, không có hashtag, không có dấu ngoặc kép, không có dấu chấm câu, không có dấu chấm phẩy, không có dấu hai chấm, không có dấu gạch ngang, không có dấu gạch chéo, không có dấu ngoặc đơn, không có dấu ngoặc kép. Chỉ cần viết đoạn văn ngắn thôi nhé. Viết bằng tiếng Việt.";
    $api_key = 'AIzaSyCF6tM5e-AqFUGMV-5KNFlc0vEVydkjNwM';
    $model_id = 'gemini-2.0-flash';
    $api_url = "https://generativelanguage.googleapis.com/v1beta/models/{$model_id}:generateContent?key={$api_key}";

    $body = json_encode([
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $prompt
                    ]
                ]
            ]
        ]
    ]);

    $args = array(
        'method'      => 'POST',
        'headers'     => array(
            'Content-Type' => 'application/json',
        ),
        'body'        => $body,
        'timeout'     => 45, // Tăng timeout nếu cần thiết
        'sslverify'   => false // Chỉ sử dụng false trong môi trường phát triển, không nên dùng trong production
    );

    $responseGemini = wp_remote_post($api_url, $args);
    $bodyCallApiGemini = wp_remote_retrieve_body($responseGemini);
    $response_array = json_decode($bodyCallApiGemini, true);
    if (isset($response_array['candidates'][0]['content']['parts'][0]['text'])) {
        $generated_text = $response_array['candidates'][0]['content']['parts'][0]['text'];
    } else {
        echo "Không tìm thấy văn bản trong phản hồi.";
    }
    $outputLinkUrl = '';

    $post_id = intval($_POST['post_id']);
    $title = get_the_title($post_id);
    $tags = get_the_tags($post_id);
    $tag_string = '';

    if ($tags) {
        $tag_names = [];

        foreach ($tags as $tag) {
            $tag_names[] = $tag->name;
        }

        $tag_string = implode(', ', $tag_names);
    }
    // Get the thumbnail URL
    $thumbnail_url = get_the_post_thumbnail_url($post_id, 'full'); // Get the full size thumbnail

    // Get the post link
    $post_link = get_permalink($post_id); // Get the post permalink
    $content = get_the_excerpt($post_id); // Replace $post_id with the ID of the post
    // Prepare the information to be shown in the textarea
    $output = "$title\n\n";
    // $output .= "$thumbnail_url\n";
    // $output .= "$content\n\n"; // Add post link
    $output .= "$generated_text\n\n"; // Add post link
    $output .= "Chi tiết tại bình luận phía dưới 👇👇👇\n"; // Add post link
    $output .= "---------------------\n"; // Add post link
    $output .= "🌐 Website: chiaseluat.vn \n"; // Add post link
    $output .= "☎️ Hotline: 0971.522.778\n"; // Add post link
    $output .= "---------------------\n"; // Add post link
    $output .= "$tag_string\n"; // Add tags
    $outputLinkUrl .= $title . "\n" . $content . "\n" . $post_link . ";\n\n";
    $posts = get_posts([
        'numberposts' => 4,
        'post_status' => 'publish',
    ]);
    foreach ($posts as $post) {
        $title         = get_the_title($post->ID);
        $post_link     = get_permalink($post->ID);
        $content       = get_the_excerpt($post->ID);
        $outputLinkUrl .= $title . "\n" . $content . "\n" . $post_link . ";\n\n";
    }
    $data = array(
        'content' => $output,
        'imageUrl' => $thumbnail_url,
        'linkUrl' => $outputLinkUrl
    );

    // Send the data as JSON and terminate the script
    echo wp_send_json($data);
    // Return all information
    // echo json_encode(array('output' => $output));

    wp_die(); // Terminate and return a proper response
}

add_action('wp_ajax_rpt_get_title', 'rpt_get_title');
function rpt_send_to_api()
{
    $dataMess = sanitize_textarea_field($_POST['message']); // Sanitize the message content

    $urlImage = sanitize_textarea_field($_POST['imageUrl']); // Sanitize the message content

    $linkUrl = sanitize_textarea_field($_POST['linkUrl']); // Sanitize the message content

    // Your Page Access Token
    $access_token = 'EAAPArrG6jWEBPJ1VykhZATy3nMcZCm6TLrBp3whAsZBASXfqCM3LPEykCAcm4xZAIflnUtUzFYyjgQH53MieiYTEbBk5cFOLMsh9rXZCforUdZANzig0c9YQTHN4AMkUYWLsRtpDKNAW5tWzD0BohbtIC8dGZCoPtT0LLYD1eHdsjodjajKIh2OokEnC329D2kudlWUIH5fl80CZBDJpyShZBOi0ZD';
    $page_id = '129058386960755'; // Replace with your Facebook Page ID
    $page_configs = [
       // [
         //   'page_id' => '129058386960755',
           // 'access_token' => 'EAAPArrG6jWEBPJ1VykhZATy3nMcZCm6TLrBp3whAsZBASXfqCM3LPEykCAcm4xZAIflnUtUzFYyjgQH53MieiYTEbBk5cFOLMsh9rXZCforUdZANzig0c9YQTHN4AMkUYWLsRtpDKNAW5tWzD0BohbtIC8dGZCoPtT0LLYD1eHdsjodjajKIh2OokEnC329D2kudlWUIH5fl80CZBDJpyShZBOi0ZD'
        //],
        [
            'page_id' => '725251044000343',
            'access_token' => 'EAAPArrG6jWEBPIvoOgFZAYsxZBOZA7cznsVMasiO3M174ZATP3ZC2M4HerLP7C77oyJSZB6y8LgsHiBq7t1ZB8kKbPT7uwHGZCfgAe8yZADEuDRjt3AMIuMlDZB2Mv1585YCtyuvBAnrftT008aCrZC9x1kuOjuUxg7jyiqZAmwCgfdohGd2UAjwjjVpquaHSFHJ4w61VSRgtjsJkXnNBBfZAuZAUe'
        ]
    ];

    foreach ($page_configs as $config) {
        $page_id = $config['page_id'];
        $access_token = $config['access_token'];

        // API endpoint for uploading the image
        $photo_url = "https://graph.facebook.com/v19.0/$page_id/photos";

        // Image URL (or you can upload an image file directly)
        $image_url = $urlImage; // Replace with your image URL

        // Message you want to post along with the image
        $message = $dataMess;

        // Set the Authorization header
        $headers = array(
            'Authorization' => 'Bearer ' . $access_token,
        );

        // First, upload the image
        $photo_data = array(
            'url' => $image_url, // Image URL
            'published' => false // Do not publish the photo yet, we want to attach it to a post
        );

        // Make the POST request to upload the image
        $photo_response = wp_remote_post($photo_url, array(
            'headers' => $headers,
            'body' => $photo_data,
            'timeout' => 30,  // Timeout set to 30 seconds
        ));

        // Check if the image upload was successful
        if (is_wp_error($photo_response)) {
            echo 'Error uploading image: ' . $photo_response->get_error_message();
        }

        // Get the response body
        $photo_body = wp_remote_retrieve_body($photo_response);
        $photo_data = json_decode($photo_body, true);

        // Check if the image ID is returned
        if (!isset($photo_data['id'])) {
            echo 'Error: Unable to retrieve image ID after upload.';
        }

        // Get the uploaded image ID
        $photo_id = $photo_data['id'];

        // Now, post the message along with the uploaded image
        $post_url = "https://graph.facebook.com/v19.0/$page_id/feed";

        // Data to post the message with the uploaded image
        $post_data = array(
            'message' => $message, // The message to go with the image
            'attached_media' => json_encode(array(array('media_fbid' => $photo_id))), // Attach the image using its ID
        );

        // Make the POST request to publish the post with the image and message
        $post_response = wp_remote_post($post_url, array(
            'headers' => $headers,
            'body' => $post_data,
            'timeout' => 30,  // Timeout set to 30 seconds
        ));

        // Check if the post request was successful
        if (is_wp_error($post_response)) {
            echo 'Error posting message with image: ' . $post_response->get_error_message();
        }

        // Get the response body
        $post_body = wp_remote_retrieve_body($post_response);
        $post_data = json_decode($post_body, true);

        // Check if the post was created
        if (isset($post_data['id'])) {
            // echo 'Post was successfully made with ID: ' . esc_html($post_data['id']);
            echo $post_data['id'];
        } else {
            echo 'Error: Unable to post the message with the image.';
        }
        /////////////////POST COMMENT//////////////////////
        $post_id_feed = $post_data['id'];
        $api_url = "https://graph.facebook.com/v19.0/{$post_id_feed}/comments";

        $first_comment_id = null;
        $lines = explode(";\n\n", $linkUrl);
        $all_success = true;
        $index = 0;

        foreach ($lines as $line) {
            // Data to be sent in the POST request
            $all_success = true;
            $line = trim($line);
            if (empty($line)) continue;

            $post_body = array(
                'message' => $line
            );

            $headers = array(
                'Authorization' => 'Bearer ' . $access_token,
            );

            $response = wp_remote_post($api_url, array(
                'body' => $post_body,
                'headers' => $headers,
                'timeout' => 30,
            ));

            if (is_wp_error($response)) {
                $all_success = false;
                break;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (!isset($data['id'])) {
                $all_success = false;
                break;
            }

            if ($index === 0) {
                $first_comment_id = $data['id'];
            }

            $index++;
        }
        if ($all_success) {
            echo '✅ All comments posted successfully!';
        } else {
            echo '❌ Some comments failed to post.';
        }

        // Step 4: Pin the first comment
        if ($first_comment_id) {
            $pin_url = "https://graph.facebook.com/v19.0/{$first_comment_id}/pins";

            $pin_response = wp_remote_post($pin_url, [
                'headers' => $headers,
                'timeout' => 30,
            ]);

            if (is_wp_error($pin_response)) {
                echo "❌ Failed to pin comment for Page ID {$page_id}: " . $pin_response->get_error_message() . "<br>";
            } else {
                echo "📌 First comment pinned successfully for Page ID {$page_id}<br>";
            }
        }
    }

    /////////////////END POST COMMENT//////////////////////
    wp_die(); // Terminate and return a proper response
}
add_action('wp_ajax_rpt_send_to_api', 'rpt_send_to_api');
