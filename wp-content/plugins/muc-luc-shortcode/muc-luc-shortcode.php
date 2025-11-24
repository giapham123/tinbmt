<?php
/**
 * Plugin Name: TOC Block
 * Description: Plugin tạo mục lục (TOC) đẹp, có thể thu gọn/mở rộng, hỗ trợ H1-H3, đánh số đa cấp.
 * Version: 1.1
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

class TOC_Block {
    public function __construct() {
        add_shortcode('muc_luc', [$this, 'render_toc']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets() {
        wp_enqueue_style('toc-block-style', plugin_dir_url(__FILE__) . 'toc-block.css');
        wp_enqueue_script('toc-block-script', plugin_dir_url(__FILE__) . 'toc-block.js', ['jquery'], false, true);
    }

    public function render_toc($atts, $content = null) {
        global $post;
        if (!$post) return '';

        $content = $post->post_content;

        // Lấy các thẻ H1-H3
        preg_match_all('/<h([1-3])[^>]*>(.*?)<\/h[1-3]>/', $content, $matches, PREG_SET_ORDER);

        if (!$matches) return '';

        // Đếm số lượng từng loại heading
        $count_h = [1 => 0, 2 => 0, 3 => 0];
        foreach ($matches as $m) {
            $count_h[(int)$m[1]]++;
        }

        $toc_items = [];
        $h1 = $h2 = $h3 = 0;

        foreach ($matches as $match) {
            $level = (int)$match[1];
            $title = strip_tags($match[2]);
            $id = $this->sanitize_title_no_diacritics($title);

            // Nếu số lượng Hx chỉ có 1 thì bỏ qua không thêm vào TOC
            if ($count_h[$level] <= 1) {
                // Nhưng vẫn cần thêm id vào content để có thể link
                $content = preg_replace(
                    '/<h' . $level . '[^>]*>' . preg_quote($match[2], '/') . '<\/h' . $level . '>/',
                    '<h' . $level . ' id="' . $id . '">' . $match[2] . '</h' . $level . '>',
                    $content,
                    1
                );
                continue;
            }

            // Đánh số thứ tự
            if ($level === 1) {
                $h1++; $h2 = 0; $h3 = 0;
                $numbering = $h1 . '.';
            } elseif ($level === 2) {
                $h2++; $h3 = 0;
                $numbering = $h1 ? $h1 . '.' . $h2 . '.' : $h2 . '.';
            } else {
                $h3++;
                if ($h1 && $h2) {
                    $numbering = $h1 . '.' . $h2 . '.' . $h3 . '.';
                } elseif ($h2) {
                    $numbering = $h2 . '.' . $h3 . '.';
                } else {
                    $numbering = $h3 . '.';
                }
            }

            // Gán id vào heading
            $content = preg_replace(
                '/<h' . $level . '[^>]*>' . preg_quote($match[2], '/') . '<\/h' . $level . '>/',
                '<h' . $level . ' id="' . $id . '">' . $match[2] . '</h' . $level . '>',
                $content,
                1
            );

            $toc_items[] = '<li class="toc-item level-' . $level . '"><a href="#' . $id . '">' . $numbering . ' ' . $title . '</a></li>';
        }

        if (empty($toc_items)) return '';

        $toc  = '<div class="toc-block">';
        $toc .= '<div class="toc-header"><strong>Mục Lục</strong><button class="toc-toggle">−</button></div>';
        $toc .= '<ul class="toc-list">' . implode('', $toc_items) . '</ul></div>';

        // Thay thế content
        remove_filter('the_content', 'wpautop');
        add_filter('the_content', function() use ($content) {
            return $content;
        });

        return $toc;
    }

    private function sanitize_title_no_diacritics($title) {
        $title = remove_accents($title);
        $title = preg_replace('/[^A-Za-z0-9\- ]/', '', $title);
        $title = str_replace(' ', '-', $title);
        return strtolower($title);
    }
}

new TOC_Block();
