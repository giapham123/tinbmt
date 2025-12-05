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

function vnexpress_header_shortcode() {
    ob_start(); 
    $weekday = ['Chủ nhật','Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7'];
    $today = $weekday[date('w')] . ', ' . date('d/m/Y');
    ?>
    <div class="vnexpress-header-container desktop-header">
        <!-- Left side: Logo + date -->
        <div class="header-left">
            <div class="vnexpress-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo esc_url(wp_get_upload_dir()['baseurl'] . '/logobackground.webp'); ?>" alt="Logo">
                </a>
            </div>
        </div>

        <!-- Right side: Trends | Newest -->
        <div class="header-right">
            <a href="<?php echo esc_url(home_url('/?page_id=572')); ?>" class="vnexpress-header-link">Nóng</a>
            <span class="separator">|</span>
            <a href="<?php echo esc_url(home_url('/newest')); ?>" class="vnexpress-header-link">Mới Nhất</a>
            <span class="separator">|</span>
            <span class="vnexpress-date"><?php echo esc_html($today); ?></span>
        </div>
    </div>
    <div class="vnexpress-hamburger">
        <span class="hamburger-icon">☰</span>
        <div class="vnexpress-logo-mobile">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <img src="<?php echo esc_url(wp_get_upload_dir()['baseurl'] . '/logobackground.webp'); ?>" alt="Logo">
            </a>
        </div>
        <div class="vnexpress-hamburger-links">
            <a href="<?php echo esc_url(home_url('/?page_id=572')); ?>">Nóng</a>
            <span>|</span>
            <a href="<?php echo esc_url(home_url('/newest')); ?>">Mới Nhất</a>
        </div>
    </div>
    <div class="vnexpress-menu-wrapper">
            <div class="container-desktop">
            <?php echo do_shortcode('[vnexpress_menu]'); ?>
        </div>
    </div>
    <?php
    return shortcode_unautop(ob_get_clean());
}
add_shortcode('vnexpress_header', 'vnexpress_header_shortcode');

