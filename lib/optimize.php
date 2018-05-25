<?php


/* Remove WP Version */
add_filter('the_generator', '__return_false');
/* /end */



/* Remove Admin bar */
show_admin_bar(false);
/* /end */



/* Remove head tags */
function guv_head_clean() {
  remove_action('wp_head', 'rsd_link');
  remove_action('wp_head', 'wp_generator');
  remove_action('wp_head', 'feed_links', 2);
  remove_action('wp_head', 'index_rel_link');
  remove_action('wp_head', 'wlwmanifest_link');
  remove_action('wp_head', 'feed_links_extra', 3);
  remove_action('wp_head', 'start_post_rel_link', 10, 0);
  remove_action('wp_head', 'parent_post_rel_link', 10, 0);
  remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
  remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
  remove_action('wp_head', 'feed_links', 2);
  remove_action('wp_head', 'feed_links_extra', 3);
  remove_action('wp_head', 'print_emoji_detection_script', 7 );
  remove_action('wp_head', 'wp_oembed_add_discovery_links');
  remove_action('wp_head', 'wp_oembed_add_host_js');
  remove_action('wp_head', 'rest_output_link_wp_head', 10);
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action('admin_print_styles', 'print_emoji_styles');
  remove_filter('the_content_feed', 'wp_staticize_emoji');
  remove_filter('comment_text_rss', 'wp_staticize_emoji');
  remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
  add_filter('use_default_gallery_style', '__return_false');
  add_filter('emoji_svg_url', '__return_false');
  add_action('wp_head', 'ob_start', 1, 0);
  add_action('wp_head', function () {
    $pattern = '/.*' . preg_quote(esc_url(get_feed_link('comments_' . get_default_feed())), '/') . '.*[\r\n]+/';
    echo preg_replace($pattern, '', ob_get_clean());
  }, 3, 0);
  global $wp_widget_factory;
  if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
    remove_action('wp_head', [$wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style']);
  }
}
add_action('init', 'guv_head_clean');
/* /end */



/* Change tag style */
function guv_tag_style($input) {
  preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);
  if (empty($matches[2])) {
    return $input;
  }
  $media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
  return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
}
add_filter('style_loader_tag', 'guv_tag_style');
/* /end */




/* Remove closing tags */
function guv_remove_closing_tags($input) {
  return str_replace(' />', '>', $input);
}
add_filter('get_avatar', 'guv_remove_closing_tags');
add_filter('comment_id_fields', 'guv_remove_closing_tags');
add_filter('post_thumbnail_html', 'guv_remove_closing_tags');
/* /end */



/* Search url default */
function wpb_change_search_url() {
    if ( is_search() && ! empty( $_GET['s'] ) ) {
        wp_redirect( home_url( "/search/" ) . urlencode( get_query_var( 's' ) ) );
        exit();
    }   
}
add_action( 'template_redirect', 'wpb_change_search_url' );
/* /end */




/* Change jquery callback */
function change_jquery_cdn() { 
  $jquery_version = wp_scripts()->registered['jquery']->ver;
  wp_deregister_script('jquery');
  wp_register_script(
    'jquery', 'https://code.jquery.com/jquery-' . $jquery_version . '.min.js', [], null, true );
  add_filter('script_loader_src', 'local_jquery_help', 10, 2);
}
add_action('wp_enqueue_scripts', 'change_jquery_cdn', 100);
/**end*/



/* Local jquery callback */
function local_jquery_help($path, $jquery_callback = null) {
  static $hel_jquery = false;
  if ($hel_jquery) {
    echo '<script>(window.jQuery && jQuery.noConflict()) || document.write(\'<script src="' . $hel_jquery .'"><\/script>\')</script>' . "\n";
    $hel_jquery = false;
  }
  if ($jquery_callback === 'jquery') {
    $hel_jquery = get_bloginfo('url') .'/dist/scripts/jquery.js'; 
  }
  return $path;
}
add_action('wp_head','local_jquery_help');
/**end*/



/* Move JS to footer */
function enqueue_footer_js() {
  remove_action('wp_head', 'wp_print_scripts');
  remove_action('wp_head', 'wp_print_head_scripts', 9);
  remove_action('wp_head', 'wp_enqueue_scripts', 1);
}
add_action('wp_enqueue_scripts', 'enqueue_footer_js');
/**end*/



/* Change tag script */
function guv_tag_script($tag) {
  return preg_replace( "/ type=['\"]text\/(javascript)['\"]/", '', $tag );
}
add_filter('script_loader_tag', 'guv_tag_script');
/* /end */