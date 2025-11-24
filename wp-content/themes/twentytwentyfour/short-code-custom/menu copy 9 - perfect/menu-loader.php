<?php
/**
 * Menu Loader - Tải logic Shortcode, CSS, và JS Styling
 * Tệp này nằm trong thư mục menu/
 */

$menu_dir = get_template_directory() . '/short-code-custom/menu/';
$menu_uri = get_template_directory_uri() . '/short-code-custom/menu/';

// A. Nhúng File PHP Shortcode (menu.php)
if ( file_exists( $menu_dir . 'menu.php' ) ) {
    require_once $menu_dir . 'menu.php';
}

// B. Nhúng File CSS và JS
function vnexpress_menu_enqueue_assets() {
    global $menu_uri; 
    // Trong menu-loader.php, thay '1.0' bằng '1.1'
wp_enqueue_style( 'vnexpress-menu-style', $menu_uri . 'menu.css', array(), '1.1', 'all' );
wp_enqueue_script( 'vnexpress-menu-script', $menu_uri . 'menu.js', array('jquery'), '1.1', true );
}
add_action( 'wp_enqueue_scripts', 'vnexpress_menu_enqueue_assets' );