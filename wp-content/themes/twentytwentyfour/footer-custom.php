<?php
// law-footer-shortcode.php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function law_footer_shortcode() {
    ob_start();
    ?>
    <style>
        .law-footer {
            background: #f1f1f1;
            color: #333;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            /* margin-top: 50px; */
        }
        .law-footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        /* Categories grid */
        .law-footer-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
            text-align: left;
        }
        .law-footer-categories a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .law-footer-categories a:hover {
            color: #c00;
        }
        /* Footer bottom layout */
        .law-footer-bottom {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }
        .law-footer-bottom > div {
            flex: 1;
            min-width: 260px;
        }
        .law-footer-bottom img {
            max-width: 100%;
            height: auto;
        }
        .law-footer-credit {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #666;
            margin-bottom: -50px !important;
        }

        /* Responsive tweaks with !important */
        @media (max-width: 768px) {
            .law-footer .law-footer-bottom {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }
            .law-footer .law-footer-bottom > div {
                border-top: 1px solid #ddd !important;
                padding-top: 20px !important;
                flex: unset !important;
                min-width: auto !important;
            }
            .law-footer .law-footer-bottom > div:first-child {
                border-top: none !important;
                padding-top: 0 !important;
            }
            .law-footer .law-footer-bottom img {
                margin: 0 auto 10px !important;
            }
            .law-footer .law-footer-bottom a {
                display: inline-block !important;
                margin: 5px !important;
            }
            .law-footer-container > hr {
                display: none !important;
            }
        }


        @media (max-width: 480px) {
            .law-footer-categories {
                grid-template-columns: 1fr 1fr !important;
                gap: 10px !important;
            }
        }
    </style>

    <footer class="law-footer">
        <div class="law-footer-container">
            
            <!-- Categories row -->
            <div class="law-footer-categories">
                <?php
                // Slugs of categories you want to exclude
                $exclude_slugs = array('ban-an', 'bieu-mau', 'chinh-sach-moi','thu-tuc-hanh-chinh-online','van-ban-phap-luat'); // replace with your slugs

                $categories = get_categories(array(
                    'orderby' => 'name',
                    'order'   => 'ASC',
                ));

                foreach ($categories as $category) {
                    if (in_array($category->slug, $exclude_slugs)) {
                        continue; // skip excluded categories
                    }

                    // Generate link without /category/ base
                    $category_link = home_url('/' . $category->slug . '/');

                    echo '<div><a href="' . esc_url($category_link) . '">' 
                        . esc_html($category->name) . '</a></div>';
                }
                
                ?>
            </div>

            <hr style="border:none; border-top:1px solid #ddd; margin:20px 0;">

            <!-- Footer bottom -->
            <div class="law-footer-bottom">
                
                <!-- Logo + Legal Info -->
               <!-- <div style="display:flex; align-items:center; gap:15px;">
    <img src="https://via.placeholder.com/150x50?text=LOGO" alt="Logo" style="max-height:50px;">
    <p style="margin:0; line-height:1.5;">
        © <?php echo date("Y"); ?> <?php bloginfo('name'); ?>.<br>
        Cấm sao chép dưới mọi hình thức nếu không có sự chấp thuận bằng văn bản.<br>
        Website: <a href="#" style="color:#c00; text-decoration:none;">www.chiaseluat.vn</a>
    </p>
</div> -->
                <div style="display:flex; align-items:center; gap:15px;">
                    <?php 
                        if (function_exists('get_custom_logo')) {
                            $custom_logo_id = get_theme_mod('custom_logo');
                            $logo = wp_get_attachment_image_src($custom_logo_id, array(100, 100)); // width x height
                            if ($logo) {
                                echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '" style="width:100px; height:100px; border-radius:50%;">';
                            }
                        }
                    ?>
                    <p style="margin:0; line-height:1.5;">
                        © <?php echo 2023 ?> <?php bloginfo('name'); ?>.<br>
                                Cấm sao chép dưới mọi hình thức nếu không có sự chấp thuận bằng văn bản.<br>
                        Website: <a href="<?php echo esc_url( untrailingslashit(home_url('/')) ); ?>" style="color:#c00; text-decoration:none;">
                            <?php echo esc_html( untrailingslashit(home_url('/')) ); ?>
                        </a>
                    </p>
                </div>
                <!-- Contact Info -->
                <div>
                    <p><strong>Liên hệ:</strong></p>
                    <p>📞 0971.522.778</p>
                    <p>📧 <a href="mailto:chiaseluat.vn@gmail.com" style="color:#c00; text-decoration:none;">tinbmt.vn@gmail.com</a></p>
                    <!-- <p>Fax: (028) 3991 4606</p> -->
                </div>

                <!-- Social / App Links -->
                <div>
                    <p><strong>Kết nối:</strong></p>
                    <!-- <div>
                        <a href="#" style="text-decoration:none;">📘 Facebook</a>
                        <a href="#" style="text-decoration:none;">💬 Zalo</a>
                        <a href="#" style="text-decoration:none;">🌐 LinkedIn</a>
                    </div> -->
                     <div class="fb-page" data-href="https://www.facebook.com/tintucbmt" data-tabs="" data-width="300" data-height="" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/tintucbmt" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/tintucbmt">Tin Tức Buôn Ma Thuột</a></blockquote></div>

    <!-- Load Facebook SDK once -->
   <div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v24.0&appId=1056281468767585"></script>
                </div>

            </div>

            <div class="law-footer-credit">
                Thiết kế bởi <strong style="color:#c00;">Tin BMT</strong>
            </div>

        </div>
    </footer>
    <?php
    return ob_get_clean();
}
add_shortcode('law_footer', 'law_footer_shortcode');
function law_footer_styles() {
    wp_register_style('law-footer-inline', false);
    wp_enqueue_style('law-footer-inline');
    wp_add_inline_style('law-footer-inline', "
        @media (max-width: 768px) {
            .law-footer .law-footer-bottom {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }
            .law-footer .law-footer-bottom > div {
                margin-top:-50px;
                border-top: 1px solid #ddd !important;
                padding-top: 20px !important;
                flex: unset !important;
                min-width: auto !important;
            }
            .law-footer .law-footer-bottom > div:first-child {
                border-top: none !important;
                padding-top: 0 !important;
            }
            .law-footer-container > hr {
                display: none !important;
            }
            .law-footer-facebook-wrapper {
                min-width: 100% !important;
                margin:-0px -130px -0px -130px !important;
        }
    ");
}
add_action('wp_enqueue_scripts', 'law_footer_styles');
