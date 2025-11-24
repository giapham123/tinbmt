<?php
// VNExpress menu shortcode
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

    $menu_html = wp_nav_menu($args);

    $home_icon = '<li class="menu-item menu-item-home"><a href="' . esc_url(home_url('/')) . '">🏠 Home</a></li>';
    $menu_html = preg_replace('/<ul class="vnexpress-main-menu">/', '<ul class="vnexpress-main-menu">' . $home_icon, $menu_html, 1);

    return $menu_html;
}
add_shortcode('vnexpress_menu', 'vnexpress_menu_shortcode');

// VNExpress header shortcode
function vnexpress_header_shortcode() {
    ob_start(); ?>
    <div class="vnexpress-header-container">
        <div class="vnexpress-logo">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <img src="<?php echo esc_url(wp_get_upload_dir()['baseurl'] . '/2025/09/background.png'); ?>" alt="Logo">
            </a>
        </div>

        <div class="vnexpress-search">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="text" name="s" placeholder="Tìm kiếm...">
                <button type="submit">🔍</button>
            </form>
            <div class="vnexpress-search-icon">🔍</div>
        </div>

        <div class="vnexpress-contact">
            <a href="tel:0123456789">📞 0123 456 789</a>
        </div>
    </div>
    <div class="vnexpress-hamburger">☰</div>
    <div class="vnexpress-menu-wrapper">
        <?php echo do_shortcode('[vnexpress_menu]'); ?>
    </div>
<?php
    return shortcode_unautop(ob_get_clean());
}
add_shortcode('vnexpress_header', 'vnexpress_header_shortcode');
