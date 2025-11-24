<?php
function vnexpress_menu_shortcode() {
    $location_slug = 'vnexpress_main_nav'; 

    if (!has_nav_menu($location_slug)) {
        if (current_user_can('manage_options')) {
            $locations = get_registered_nav_menus();
            $friendly_name = isset($locations[$location_slug]) ? $locations[$location_slug] : 'Menu Chính VnExpress';
            return '<p style="color:red;text-align:center;">Vui lòng gán menu vào vị trí "' . esc_html($friendly_name) . '"</p>';
        }
        return '';
    }

    $args = array(
        'theme_location' => $location_slug,
        'container' => 'nav',
        'container_class' => 'vnexpress-main-nav-container',
        'menu_class' => 'vnexpress-main-menu',
        'echo' => false,
        'depth' => 0,
        'fallback_cb' => false,
    );

    // Lấy menu HTML
    $menu_html = wp_nav_menu($args);

    // Thêm icon Home vào đầu menu
    $home_icon = '<li class="menu-item menu-item-home"><a href="' . esc_url(home_url('/')) . '">🏠 Home</a></li>';

    // Chèn trước các item khác
    $menu_html = preg_replace('/<ul class="vnexpress-main-menu">/', '<ul class="vnexpress-main-menu">' . $home_icon, $menu_html, 1);

    return $menu_html;
}
add_shortcode('vnexpress_header', 'vnexpress_menu_shortcode');

