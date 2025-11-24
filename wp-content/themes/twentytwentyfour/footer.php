<?php
// Footer Shortcode for chiaseluat.vn (only page header, no timeline)
function custom_footer_shortcode() {
    ob_start();
    ?>
    <div style="min-width:180px;max-width:500px;width:100%;margin:0 auto;">
        <div class="fb-page" 
             data-href="https://www.facebook.com/csl.ngocnguyen" 
             data-tabs="" 
             data-width="500" 
             data-height="130" 
             data-small-header="false" 
             data-adapt-container-width="true" 
             data-hide-cover="false" 
             data-show-facepile="false">
        </div>
    </div>

    <!-- Load Facebook SDK once -->
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" 
        src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v18.0" nonce="<?php echo wp_generate_password(10, false); ?>">
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_footer', 'custom_footer_shortcode');
