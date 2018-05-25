<?php



/* Author restricted */
add_filter('init', create_function('$a', 'global $wp_rewrite; $wp_rewrite->author_base = ""; $wp_rewrite->flush_rules();'));
function del_auth_prmlnk() {
  global $wp_rewrite;
  $wp_rewrite->author_base = '';
  $wp_rewrite->author_structure = "/404/" . $wp_rewrite->author_base;
  add_rewrite_rule('usuario/([^/]+)/?$', 'index.php?author_name=$matches[1]', 'top');
}
add_action('init','del_auth_prmlnk');
/**end*/



/* Delete html in comments */
add_filter('pre_comment_content', 'wp_specialchars');
/**end*/



/* Delete login errors */
function ddw_log_err_msg() {
return '';
}
add_filter('login_errors', 'ddw_log_err_msg');
/**end*/



/* Remove acces wp-admin if no login */
add_action( 'init', 'blockusers_init' );
function blockusers_init() {
    // If accessing the admin panel and not an admin
    if ( is_admin() && !is_user_logged_in() ) {
        wp_redirect( site_url('/404') );
        exit;
    }
}
/**end*/



/* Filter_xmlrpc_method */
function filter_xmlrpc_method($methods) {
  unset($methods['pingback.ping']);
  return $methods;
}
add_filter('xmlrpc_methods', 'filter_xmlrpc_method', 10, 1);
/**end*/



/* Filter_headers */
function filter_headers($headers) {
  if (isset($headers['X-Pingback'])) {
    unset($headers['X-Pingback']);
  }
  return $headers;
}
add_filter('wp_headers', 'filter_headers', 10, 1);
/**end*/


/* Filter_rewrites */
function filter_rewrites($rules) {
  foreach ($rules as $rule => $rewrite) {
    if (preg_match('/trackback\/\?\$$/i', $rule)) {
      unset($rules[$rule]);
    }
  }
  return $rules;
}
add_filter('rewrite_rules_array', 'filter_rewrites');
/**end*/



/* Kill_pingback_url */
function kill_pingback_url($output, $show) {
  if ($show === 'pingback_url') {
    $output = '';
  }
  return $output;
}
add_filter('bloginfo_url', 'kill_pingback_url', 10, 2);
/**end*/



/* Kill_xmlrpc */
function kill_xmlrpc($action) {
  if ($action === 'pingback.ping') {
    wp_die('Pingbacks are not supported', 'Not Allowed!', ['response' => 403]);
  }
}
add_action('xmlrpc_call', 'kill_xmlrpc');
/**end*/