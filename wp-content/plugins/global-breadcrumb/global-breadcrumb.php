<?php
/*
Plugin Name: Global Breadcrumb
Description: Global breadcrumb plugin that shows navigation right under the post/page title. Shows categories without the /category/ prefix.
Version: 2.5
Author: Your Name
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Render Breadcrumb
 */
function gb_display_breadcrumb() {
    if ( is_front_page() || is_home() ) return;

    echo '<nav class="gb-breadcrumb-container"><div class="gb-breadcrumb" aria-label="Breadcrumb">';
    echo '<a href="' . esc_url(home_url()) . '">Home</a>';
    $sep = '<span class="separator">›</span>';

    if ( is_single() ) {

        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            $cat = $categories[0];
            $cat_url = home_url( '/' . $cat->slug );
            echo " $sep <a href='" . esc_url( $cat_url ) . "'>" . esc_html( $cat->name ) . "</a>";
        }

        echo " $sep " . esc_html( get_the_title() );

    } elseif ( is_page() ) {

        echo " $sep " . esc_html( get_the_title() );

    } elseif ( is_category() ) {

        $cat = get_queried_object();
        if ( $cat && isset( $cat->slug ) ) {
            echo " $sep <a href='" . esc_url( home_url( '/' . $cat->slug ) ) . "'>" . esc_html( $cat->name ) . "</a>";
        }

    } elseif ( is_search() ) {

        echo " $sep Search results for: " . esc_html( get_search_query() );

    } elseif ( is_404() ) {

        echo " $sep 404 Not Found";
    }

    echo '</div></nav>';
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
add_shortcode( 'global_breadcrumb', 'gb_breadcrumb_shortcode' );

/**
 * Enqueue breadcrumb CSS
 */
add_action( 'wp_enqueue_scripts', function() {

    wp_register_style( 'gb-breadcrumb-style', false );
    wp_enqueue_style( 'gb-breadcrumb-style' );

    wp_add_inline_style( 'gb-breadcrumb-style', "
        .gb-breadcrumb-container {
            width: auto;
            background: #f9f9f9;
            border-bottom: 1px solid #eee;
            padding-left: 16px;
            padding-right: 16px;
        }

        .gb-breadcrumb {
            max-width: 1200px;
            margin: 0 auto;
            padding: 8px 0;
            font-size: 14px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px;
        }

        .gb-breadcrumb a {
            color: #0073aa;
            text-decoration: none;
            white-space: nowrap;
        }

        .gb-breadcrumb a:hover {
            text-decoration: underline;
        }

        .gb-breadcrumb .separator {
            color: #999;
            padding: 0 4px;
        }

        @media (max-width: 768px) {
            .gb-breadcrumb {
                font-size: 13px;
            }
            .gb-breadcrumb a {
                max-width: 120px;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }

        @media (max-width: 480px) {
            .gb-breadcrumb {
                font-size: 12px;
            }
            .gb-breadcrumb a {
                max-width: 100px;
            }
        }
    ");
});
