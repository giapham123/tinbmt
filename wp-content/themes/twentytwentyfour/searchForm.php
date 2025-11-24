<?php
function search_form_shortcode() {
    ob_start(); // Start output buffering
    ?>
    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
        <span class="screen-reader-text"><?php echo _x('Search for:', 'label'); ?></span>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x('Tìm Kiếm …', 'placeholder'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
        <button type="submit" class="search-submit"><?php echo esc_attr_x('Search', 'submit button'); ?></button>
    </form>
    <?php
    return ob_get_clean(); // Return the buffered content
}
?>