<?php
/*
Plugin Name: Daklak Bao Moi Link Crawler
Plugin URI: https://example.com
Description: Crawl .epi links from Baomoi and display original URLs in the admin menu via button.
Version: 1.2
Author: Your Name
Author URI: https://example.com
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// ==========================
// MAIN CRAWLER FUNCTION
// ==========================
if (!function_exists('dbmlc_get_original_links')) {
    function dbmlc_get_original_links() {
        $url = "https://baomoi.com/dak-lak-tag351.epi";
        $html = file_get_contents($url);

        if (!$html) return [];

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // Tìm các link .epi trong danh sách bài viết
        $nodes = $xpath->query(
            '//*[contains(@class,"content-list")]//h3//a[contains(@href,".epi")]'
        );

        $epiLinks = [];
        foreach ($nodes as $node) {
            $epiLinks[] = $node->getAttribute('href');
        }

        $originalUrls = [];
        $count = 0;

        foreach ($epiLinks as $link) {
            if ($count >= 3) break;

            $epiHtml = @file_get_contents("https://baomoi.com" . $link);
            if (!$epiHtml) continue;

            if (preg_match_all('/originalUrl":"(https?:\/\/[^"]+)"/', $epiHtml, $matches)) {
                $originalUrl = end($matches[1]);
                $originalUrls[] = $originalUrl;
                $count++;
            }
        }

        return array_unique($originalUrls);
    }
}

// ==========================
// ADMIN PAGE
// ==========================
if (!function_exists('dbmlc_menu_page')) {
    function dbmlc_menu_page() {
        ?>
        <div class="wrap">
            <h1>Daklak Bao Moi Link Crawler</h1>

            <form method="post">
                <input type="hidden" name="dbmlc_fetch_links" value="1">
                <button type="submit" class="button button-primary">
                    Get Links
                </button>
            </form>

            <div style="margin-top:20px;">
                <?php
                // Xử lý khi nhấn nút GET LINKS
                if (!empty($_POST['dbmlc_fetch_links'])) {
                    $originalLinks = dbmlc_get_original_links();

                    if (empty($originalLinks)) {
                        echo "<p>Không tìm được link gốc.</p>";
                    } else {
                        echo "<ul>";
                        foreach ($originalLinks as $link) {
                            echo "<li><a href='{$link}' target='_blank'>{$link}</a></li>";
                        }
                        echo "</ul>";
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }
}

// ==========================
// ADD MENU
// ==========================
if (!function_exists('dbmlc_add_admin_menu')) {
    function dbmlc_add_admin_menu() {
        add_menu_page(
            'Daklak Bao Moi Link Crawler',
            'Daklak Links',
            'manage_options',
            'daklak-bao-moi-link-crawler',
            'dbmlc_menu_page',
            'dashicons-admin-links',
            20
        );
    }
}
add_action('admin_menu', 'dbmlc_add_admin_menu');
