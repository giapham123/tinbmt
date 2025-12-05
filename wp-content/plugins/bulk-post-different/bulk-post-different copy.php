<?php


if (!defined('ABSPATH')) exit;

add_action('init', function() {
    if(!session_id()) session_start();
});

/*--------------------------------------------------------------
  ADMIN MENU
--------------------------------------------------------------*/
add_action('admin_menu', 'bpp_admin_menu');
function bpp_admin_menu() {
    add_menu_page(
        'Bulk Post Pro Advanced',
        'Bulk Post Pro Advanced',
        'manage_options',
        'bulk-post-pro-advanced',
        'bpp_admin_page',
        'dashicons-edit',
        6
    );
}

/*--------------------------------------------------------------
  ADMIN PAGE
--------------------------------------------------------------*/
function bpp_admin_page() {
     // --- Clear session cache mỗi lần load page ---
    // unset($_SESSION['bpp_temp_rows']);
    // unset($_SESSION['bpp_temp_images']);
    // unset($_SESSION['bpp_textarea']);
    // unset($_SESSION['bpp_json_api']);
    // unset($_SESSION['bpp_temp_txt']);
    // unset($_SESSION['bpp_temp_csv']);
?>
<div class="wrap">
    <h1>Bulk Post Pro Advanced</h1>
    <form method="post" enctype="multipart/form-data">

        <h2>1) Input Sources</h2>
        <h3>Textarea</h3>
        <textarea name="bpp_textarea" rows="10" style="width:100%;"><?= isset($_SESSION['bpp_textarea']) ? esc_textarea($_SESSION['bpp_textarea']) : ''; ?></textarea>

        <h3>Upload TXT</h3>
        <input type="file" name="bpp_txt">

        <h3>Upload CSV</h3>
        <input type="file" name="bpp_csv">

        <h3>JSON API URL</h3>
        <input type="text" name="bpp_json_api" style="width:100%;" placeholder="https://example.com/api/posts" value="<?= isset($_SESSION['bpp_json_api']) ? esc_attr($_SESSION['bpp_json_api']) : ''; ?>">

        <h3>Upload Local Images</h3>
        <input type="file" name="bpp_local_images[]" multiple>

        <hr>
        <h2>Preview / Generate</h2>
        <button type="submit" name="bpp_preview" class="button button-primary">Preview Posts</button>
        <button type="submit" name="bpp_generate" class="button button-secondary">Generate Posts</button>

        <input type="hidden" id="bpp_temp_json" name="bpp_temp_json" value="">
    </form>

<?php
    if (isset($_POST['bpp_preview'])) {
        bpp_preview_posts();
    }
    if (isset($_POST['bpp_generate'])) {
        bpp_handle_submission();
    }
?>
</div>

<script>
jQuery(document).ready(function($){
    $('form').on('submit', function(e){
        var activeName = $(document.activeElement).attr('name');
        if(activeName==='bpp_generate' || activeName==='bpp_preview'){
            var rows = [];
            var textarea = $('textarea[name="bpp_textarea"]').val().trim();
            if(textarea){
                textarea.split(/\r\n|\n/).forEach(line=>{
                    if(line.trim()!=='') rows.push(line.trim());
                });
            }
            $('#bpp_temp_json').val(JSON.stringify(rows));
        }
    });
});
</script>

<?php
}

/*--------------------------------------------------------------
  COLLECT INPUTS
--------------------------------------------------------------*/
function bpp_collect_input_rows() {
    $rows = [];

    if(isset($_SESSION['bpp_temp_rows']) && !empty($_SESSION['bpp_temp_rows'])){
        return $_SESSION['bpp_temp_rows'];
    }

    // Textarea
    if(!empty($_POST['bpp_textarea'])){
        $lines = preg_split("/\r\n|\n|\r/", trim($_POST['bpp_textarea']));
        foreach($lines as $line){
            if(trim($line)!=='') $rows[] = trim($line);
        }
        $_SESSION['bpp_textarea'] = $_POST['bpp_textarea'];
    }

    $upload_dir = wp_upload_dir();
    $temp_file_dir = trailingslashit($upload_dir['basedir'])."bpp_temp_files/";
    if(!file_exists($temp_file_dir)) wp_mkdir_p($temp_file_dir);

    // TXT
    if(!empty($_FILES['bpp_txt']['tmp_name'])){
        $txt_tmp = $_FILES['bpp_txt']['tmp_name'];
        $txt_name = basename($_FILES['bpp_txt']['name']);
        $txt_path = $temp_file_dir.$txt_name;
        move_uploaded_file($txt_tmp, $txt_path);
        $_SESSION['bpp_temp_txt'] = $txt_path;

        $lines = preg_split("/\r\n|\n|\r/", file_get_contents($txt_path));
        foreach($lines as $line){
            if(trim($line)!=='') $rows[] = trim($line);
        }
    }

    // CSV
    if(!empty($_FILES['bpp_csv']['tmp_name'])){
        $csv_tmp = $_FILES['bpp_csv']['tmp_name'];
        $csv_name = basename($_FILES['bpp_csv']['name']);
        $csv_path = $temp_file_dir.$csv_name;
        move_uploaded_file($csv_tmp, $csv_path);
        $_SESSION['bpp_temp_csv'] = $csv_path;

        if(($handle = fopen($csv_path,"r"))!==FALSE){
            while(($data = fgetcsv($handle))!==FALSE){
                if(!empty($data[0])){
                    $rows[] = implode('|', array_map('trim', $data));
                }
            }
            fclose($handle);
        }
    }

    // JSON API
    if(!empty($_POST['bpp_json_api'])){
        $json_url = esc_url_raw($_POST['bpp_json_api']);
        $_SESSION['bpp_json_api'] = $json_url;
        $response = wp_remote_get($json_url);
        if(!is_wp_error($response)){
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if(is_array($data)){
                foreach($data as $item){
                    $title   = $item['title'] ?? '';
                    $content = $item['content'] ?? '';
                    $slug    = $item['slug'] ?? '';
                    $meta    = $item['meta_description'] ?? '';
                    $focus   = $item['focus_keyword'] ?? '';
                    $tags    = is_array($item['tags'] ?? null) ? implode(',', $item['tags']) : ($item['tags'] ?? '');
                    $image   = $item['image'] ?? '';
                    $cat     = is_array($item['categories'] ?? null) ? implode(',', $item['categories']) : ($item['categories'] ?? '');
                    $rows[] = implode('|', [$title,$content,$slug,$meta,$focus,$tags,$image,$cat]);
                }
            }
        }
    }

    $_SESSION['bpp_temp_rows'] = $rows;

    return $rows;
}

/*--------------------------------------------------------------
  SAVE TEMP IMAGES
--------------------------------------------------------------*/
function bpp_save_temp_images() {
    $saved = [];
    if(empty($_FILES['bpp_local_images']['tmp_name'])) return $saved;

    $upload_dir = wp_upload_dir();
    $temp_dir = trailingslashit($upload_dir['basedir'])."bpp_temp/";
    if(!file_exists($temp_dir)) wp_mkdir_p($temp_dir);

    foreach($_FILES['bpp_local_images']['tmp_name'] as $i=>$tmp){
        if(!file_exists($tmp)) continue;
        $name = sanitize_file_name($_FILES['bpp_local_images']['name'][$i]);
        $new_name = time()."-".wp_generate_password(6,false)."-".$name;
        $path = $temp_dir.$new_name;
        move_uploaded_file($tmp,$path);
        $saved[] = ['path'=>$path,'url'=>trailingslashit($upload_dir['baseurl'])."bpp_temp/".$new_name];
    }

    $_SESSION['bpp_temp_images'] = $saved;

    return $saved;
}

/*--------------------------------------------------------------
  PREVIEW
--------------------------------------------------------------*/
function bpp_preview_posts(){
    $rows = bpp_collect_input_rows();
    $temp_images = bpp_save_temp_images();

    if(empty($rows)){
        echo '<p style="color:red;">No input found.</p>';
        return;
    }

    echo '<h2>Preview</h2>';
    echo '<table class="widefat striped"><thead>
            <tr><th>#</th><th>Title</th><th>Content</th><th>Slug</th><th>Meta</th><th>Focus Key</th><th>Tags</th><th>Category</th><th>Image</th></tr>
          </thead><tbody>';

    foreach($rows as $i=>$line){
        $parts = array_map('trim', explode('|',$line));
        $title = $parts[0]??'';
        $content = $parts[1]??'';
        $slug = $parts[2]??'';
        $meta = $parts[3]??'';
        $focus = $parts[4]??'';
        $tags = $parts[5]??'';
        $image = $temp_images[$i]['url'] ?? ($parts[6]??'');
        $cat = $parts[7]??'';

        echo "<tr>
            <td>".($i+1)."</td>
            <td>".esc_html($title)."</td>
            <td>".esc_html(wp_trim_words($content,25))."</td>
            <td>".esc_html($slug)."</td>
            <td>".esc_html($meta)."</td>
            <td>".esc_html($focus)."</td>
            <td>".esc_html($tags)."</td>
            <td>".esc_html($cat)."</td>";

        if(!empty($image)){
            echo "<td><img src='$image' style='max-width:100px;'></td>";
        }else{
            echo "<td>-</td>";
        }
        echo "</tr>";
    }

    echo "</tbody></table>";
    echo "<input type='hidden' name='bpp_temp_json' value='".esc_attr(json_encode($rows))."'>";
}

/*--------------------------------------------------------------
  GENERATE POSTS
--------------------------------------------------------------*/
function bpp_generate_posts(){
    $rows = bpp_collect_input_rows();
    $temp_images = $_SESSION['bpp_temp_images'] ?? [];

    if(empty($rows)){
        echo "<div class='notice notice-error'><p>No input found to generate posts.</p></div>";
        return;
    }

    $count=0;
    foreach($rows as $i=>$line){
        $parts = array_map('trim', explode('|',$line));

        $title = $parts[0]??'Auto Post #'.($i+1);
        $content = $parts[1]??'';
        $slug = $parts[2]??'';
        $meta = $parts[3]??'';
        $focus = $parts[4]??'';
        $tags = $parts[5]??'';
        $image_path = $temp_images[$i]['path'] ?? null;
        $categories = $parts[7]??'';

        $post_arr = [
            'post_title'=>wp_strip_all_tags($title),
            'post_content'=>wp_kses_post($content),
            'post_status'=>'publish',
            'post_type'=>'post'
        ];

        if(!empty($slug)){
            $post_arr['post_name'] = sanitize_title($slug);
        }

        $post_id = wp_insert_post($post_arr);
        if(is_wp_error($post_id)) continue;

        // Featured image
        if($image_path){
            require_once(ABSPATH.'wp-admin/includes/image.php');
            require_once(ABSPATH.'wp-admin/includes/file.php');
            require_once(ABSPATH.'wp-admin/includes/media.php');

            $file_array = ['name'=>basename($image_path),'tmp_name'=>$image_path];
            $image_id = media_handle_sideload($file_array, $post_id);
            if(!is_wp_error($image_id)) set_post_thumbnail($post_id, $image_id);
        }

        // Tags
        if(!empty($tags)){
            wp_set_post_tags($post_id, explode(',', $tags), true);
        }

        // Categories
        if(!empty($categories)){
            $cat_arr = [];
            foreach(explode(',', $categories) as $c){
                $c = trim($c);
                if($c){
                    $term = term_exists($c, 'category');
                    if(!$term) $term = wp_insert_term($c, 'category');
                    if(!is_wp_error($term)){
                        $cat_arr[] = $term['term_id'] ?? $term['term_id'];
                    }
                }
            }
            if($cat_arr) wp_set_post_categories($post_id, $cat_arr);
        }

        // Meta description & focus keyword (Yoast)
        if(!empty($meta)) update_post_meta($post_id,'_yoast_wpseo_metadesc',$meta);
        if(!empty($focus)) update_post_meta($post_id,'_yoast_wpseo_focuskw',$focus);

        $count++;
    }

    echo "<div class='notice notice-success'><p>Created <strong>$count</strong> posts.</p></div>";

    unset($_SESSION['bpp_temp_rows']);
    unset($_SESSION['bpp_temp_images']);
}
function bpp_handle_submission(){ bpp_generate_posts(); }
