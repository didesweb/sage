<?php

function ddw_flush_rewrites() {
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}
add_action('admin_init', 'ddw_flush_rewrites');

function ddw_change_path($content) {
  $theme_name = next(explode('/themes/', get_stylesheet_directory()));
  global $wp_rewrite;
  $guv_new_non_wp_rules = array(
    'dist/styles/(.*)'      => 'wp-content/themes/'. $theme_name . '/dist/styles/$1',
    'dist/scripts/(.*)'     => 'wp-content/themes/'. $theme_name . '/dist/scripts/$1', 
    'dist/fonts/(.*)'     => 'wp-content/themes/'. $theme_name . '/dist/fonts/$1',
    'dist/images/(.*)'      => 'wp-content/uploads/$1'
  );
  $wp_rewrite->non_wp_rules += $guv_new_non_wp_rules;
}
add_action('generate_rewrite_rules', 'ddw_change_path');

function ddw_filter_path($content) {
    $theme_name = next(explode('/themes/', $content));
    $current_path = '/wp-content/themes/' . $theme_name;
    $new_path = '';
    $content = str_replace($current_path, $new_path, $content);
    return $content;
}
if (!is_admin()) { 
  add_filter('bloginfo', 'ddw_filter_path');
  add_filter('stylesheet_directory_uri', 'ddw_filter_path');
  add_filter('template_directory_uri', 'ddw_filter_path');
}