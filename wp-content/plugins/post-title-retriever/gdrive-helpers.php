<?php
// Avoid duplicate declarations
if (!function_exists('extract_drive_id_1')) {
    function extract_drive_id_1($url) {
        if (preg_match('/[-\w]{25,}/', $url, $matches)) {
            return $matches[0];
        }
        return null;
    }
}

if (!function_exists('get_gdrive_thumbnail_url')) {
    function get_gdrive_thumbnail_url($post_id, $size = 'full') {
        $thumb_id = get_post_thumbnail_id($post_id);
        if (!$thumb_id) return false;

        $file_path = get_attached_file($thumb_id);
        if ($file_path && file_exists($file_path)) {
            $thumb_src = wp_get_attachment_image_src($thumb_id, $size);
            return $thumb_src ? $thumb_src[0] : false;
        }

        $gdrive_meta = get_post_meta($thumb_id, '_gdrive_link', true);
        if (!$gdrive_meta) return false;

        $gdrive_id = extract_drive_id_1($gdrive_meta);
        if (!$gdrive_id) return false;

        return "https://drive.google.com/uc?export=view&id={$gdrive_id}";
    }
}
