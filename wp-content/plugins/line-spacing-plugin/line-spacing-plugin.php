<?php
/*
Plugin Name: Line Spacing Plugin
Description: Adds line spacing controls to the WordPress editor.
Version: 2.0
Author: Your Name
*/

// Hook into the 'init' action to add functionality
add_action('init', 'lsp_enqueue_scripts');

function lsp_enqueue_scripts() {
    if (is_admin()) {
        $version = '2.0'; // Update this version number whenever you make changes

        // Enqueue TinyMCE plugin JavaScript file
        wp_enqueue_script('lsp-tinymce-plugin', plugin_dir_url(__FILE__) . 'lsp-tinymce-plugin.js', array('jquery'), $version, true);

        // Enqueue TinyMCE plugin CSS file
        wp_enqueue_style('lsp-admin-css', plugin_dir_url(__FILE__) . 'lsp-admin.css', array(), $version);
    }
}

// Hook into the 'mce_buttons' and 'mce_external_plugins' actions for Classic Editor
add_filter('mce_buttons', 'lsp_add_mce_button');
add_filter('mce_external_plugins', 'lsp_add_mce_plugin');

function lsp_add_mce_button($buttons) {
    array_push($buttons, 'line_spacing');
    return $buttons;
}

function lsp_add_mce_plugin($plugins) {
    $plugins['line_spacing'] = plugin_dir_url(__FILE__) . 'lsp-tinymce-plugin.js';
    return $plugins;
}