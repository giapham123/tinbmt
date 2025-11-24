  <!-- $current_url = "https://chiaseluat.vn"; -->
<?php
function facebook_comments_shortcode($atts) {
    global $wp;
    $current_url = home_url(add_query_arg([], $wp->request));

    $atts = shortcode_atts(
        [
            'width'     => '100%',
            'num_posts' => '5',
            'app_id'    => '1056281468767585'
        ],
        $atts,
        'facebook_comments'
    );

    ob_start();
    ?>
    <!-- Facebook SDK -->
    <!-- <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" 
      src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v19.0&appId=<?php echo esc_attr($atts['app_id']); ?>&autoLogAppEvents=1">
    </script> -->

    <!-- <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '<?php echo esc_attr($atts['app_id']); ?>',
          cookie     : true,
          xfbml      : true,
          version    : 'v19.0'
        });
      };
    </script> -->

    <!-- Facebook Comments Wrapper -->
    <div class="fb-comments-wrapper">
        <div class="fb-comments"
             data-href="<?php echo esc_url($current_url); ?>"
             data-width="<?php echo esc_attr($atts['width']); ?>"
             data-numposts="<?php echo esc_attr($atts['num_posts']); ?>">
        </div>
    </div>

    <style>
        .fb-comments-wrapper {
            max-width: 100%;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .fb-comments,
        .fb-comments iframe {
            width: 100% !important;
        }
        .fb-comments-wrapper::before {
            content: "💬 Bình luận trên Facebook";
            display: block;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1877f2;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('facebook_comments', 'facebook_comments_shortcode');

