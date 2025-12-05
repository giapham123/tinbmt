<?php
// wp-upload-filters.php

if (!defined('ABSPATH')) {
    exit; // Ngăn chặn truy cập trực tiếp
}

// ==============================
// Convert uploaded image to WebP
// ==============================
add_filter('wp_handle_upload', 'convert_image_to_webp');

function convert_image_to_webp($file) {
    $image_path = $file['file'];
    $image_type = mime_content_type($image_path);

    // Only convert JPG/PNG
    if ($image_type == 'image/jpeg' || $image_type == 'image/png') {
        $img = null;

        if ($image_type == 'image/jpeg') {
            $img = imagecreatefromjpeg($image_path);
        } elseif ($image_type == 'image/png') {
            $img = imagecreatefrompng($image_path);
        }

        if ($img) {
            $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $image_path);

            // Convert to webp (quality 50)
            imagewebp($img, $webp_path, 50);
            imagedestroy($img);

            // Delete old file
            unlink($image_path);

            // Update return file
            $file['file'] = $webp_path;
            $file['url']  = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file['url']);
            $file['type'] = 'image/webp';
        }
    }

    return $file;
}

// ==============================
// Rename uploaded file
// ==============================
add_filter('sanitize_file_name', 'rename_uploaded_file_tinbmt', 10);

function rename_uploaded_file_tinbmt($filename) {
    $info = pathinfo($filename);
    $ext  = isset($info['extension']) ? strtolower($info['extension']) : '';

    // Tạo tên mới: tinbmt-YYYYMMDD-HHMMSS
    $new_name = 'tinbmt-' . date('Ymd-His');

    return $new_name . '.' . $ext;
}
