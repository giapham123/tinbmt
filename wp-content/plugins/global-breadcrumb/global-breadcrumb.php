<?php
/*
Plugin Name: Global Breadcrumb
Description: Global breadcrumb plugin that shows navigation right under the post/page title. Lets you select a menu item per post and shows categories without the /category/ prefix.
Version: 2.4
Author: Your Name
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add meta box to select Menu Item
 */
add_action('add_meta_boxes', function() {
    add_meta_box('gb_menu_item', 'Breadcrumb Menu Item', 'gb_menu_item_box', 'post', 'side');
});

function gb_menu_item_box($post) {
    $selected = get_post_meta($post->ID, '_gb_menu_item', true);
    $menus = wp_get_nav_menus();
    if (empty($menus)) {
        echo '<p><em>No menus found. Please create a menu first.</em></p>';
        return;
    }

    echo '<label for="gb_menu_item">Select breadcrumb menu item:</label><br>';
    echo '<select name="gb_menu_item" id="gb_menu_item" style="width:100%">';
    echo '<option value="">-- None --</option>';

    foreach ($menus as $menu) {
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        if ($menu_items) {
            echo '<optgroup label="'. esc_html($menu->name) .'">';
            foreach ($menu_items as $item) {
                $is_selected = selected($selected, $item->ID, false);
                echo '<option value="'. $item->ID .'" '. $is_selected .'>'. esc_html($item->title) .'</option>';
            }
            echo '</optgroup>';
        }
    }

    echo '</select>';
}

/**
 * Save selected menu item
 */
add_action('save_post', function($post_id) {
    if (isset($_POST['gb_menu_item'])) {
        update_post_meta($post_id, '_gb_menu_item', intval($_POST['gb_menu_item']));
    }
});

/**
 * Get selected menu item
 */
function gb_get_selected_menu_item($post_id) {
    $menu_item_id = get_post_meta($post_id, '_gb_menu_item', true);
    if (!$menu_item_id) return null;

    $menu_item = wp_setup_nav_menu_item(get_post($menu_item_id));
    return $menu_item;
}

/**
 * Render Breadcrumb
 */
function gb_display_breadcrumb() {
    if ( is_front_page() || is_home() ) return;

    echo '<nav class="gb-breadcrumb" aria-label="Breadcrumb">';
    echo '<a href="' . esc_url(home_url()) . '">Home</a>';

    $sep = '<span class="separator">›</span>';

    if ( is_single() ) {
        $menu_item = gb_get_selected_menu_item(get_the_ID());
        if ($menu_item) {
            echo " $sep <a href='" . esc_url($menu_item->url) . "'>" . esc_html($menu_item->title) . "</a>";
        }

        $categories = get_the_category();
        if (!empty($categories)) {
            $cat = $categories[0];
            $cat_url = home_url('/' . $cat->slug);
            echo " $sep <a href='" . esc_url($cat_url) . "'>" . esc_html($cat->name) . "</a>";
        }

        echo " $sep " . esc_html(get_the_title());

    } elseif ( is_page() ) {
        echo " $sep " . esc_html(get_the_title());

    } elseif ( is_category() ) {
        $cat = get_queried_object();
        if ($cat && isset($cat->slug)) {
            echo " $sep <a href='" . esc_url(home_url('/' . $cat->slug)) . "'>" . esc_html($cat->name) . "</a>";
        }

    } elseif ( is_search() ) {
        echo " $sep Search results for: " . esc_html(get_search_query());

    } elseif ( is_404() ) {
        echo " $sep 404 Not Found";
    }

    echo '</nav>';
}

/**
 * Breadcrumb Shortcode
 * Usage: [global_breadcrumb]
 */
function gb_breadcrumb_shortcode() {
    ob_start();
    gb_display_breadcrumb();
    return ob_get_clean();
}
add_shortcode('global_breadcrumb', 'gb_breadcrumb_shortcode');

/**
 * Enqueue breadcrumb CSS
 */
add_action('wp_enqueue_scripts', function() {
    wp_add_inline_style('wp-block-library', "
        .gb-breadcrumb {
            font-size: 14px;
            font-weight: 400;
            margin: 15px 0 20px;
            padding: 8px 12px;
        }
        .gb-breadcrumb a {
            color: #0073aa;
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }
        .gb-breadcrumb a:hover {
            color: #005177;
            text-decoration: underline;
        }
        .gb-breadcrumb .separator {
            margin: 0 6px;
            color: #999;
        }
    ");
});
