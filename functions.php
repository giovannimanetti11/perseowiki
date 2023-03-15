<?php

define("THEME_DIR", get_template_directory_uri());

function perseowiki_support() {

  /*
   * Add support for core custom logo.
   *
   * @link https://codex.wordpress.org/Theme_Logo
   */

  add_theme_support( 
    'custom-logo', [
    'width'       => 180,
    'height'      => 50,
    'flex-width'  => true,
    'flex-height' => true,
  ]);

  /*
   * Add featured images to post and pages.
   *
   * @link https://codex.wordpress.org/Post_Thumbnails
   */

   add_theme_support('post-thumbnails', array(
    'post',
    'page',
    'custom-post-type-name',
    ));

}

add_action( 'after_setup_theme', 'perseowiki_support' );

  /*
   * 
   * Add custom menu.
   *
   */

function perseowiki_nav_menus() {
  
  // This theme uses wp_nav_menu() in one location.

  register_nav_menus([
    'perseowiki-primary-menu' => esc_html__( 'Primary Menu', 'perseowiki' ),
    'perseowiki-category-menu' => esc_html__( 'Category Menu', 'perseowiki' ),
  ]);
}

add_action( 'init', 'perseowiki_nav_menus' );


/*
   * 
   * Add custom styles and scripts.
   *
   */

function perseowiki_styles() {

  wp_enqueue_style('perseowiki-style', get_template_directory_uri().'/style.css');
  wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' );
  wp_enqueue_style( 'font-awesome-free', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css' );
  wp_enqueue_script( 'custom-functions', get_template_directory_uri().'/inc/js/functions.js' );

  wp_localize_script( 'custom-functions', 'myAjax', array( 'ajax_url' => admin_url('admin-ajax.php') ) );
}

add_action('wp_enqueue_scripts', 'perseowiki_styles');


  /*
     * 
     * Add svg support .
     *
     */

function add_svg_to_upload_mimes( $upload_mimes ) {
	$upload_mimes['svg'] = 'image/svg+xml';
	$upload_mimes['svgz'] = 'image/svg+xml';
	return $upload_mimes;
}
add_filter( 'upload_mimes', 'add_svg_to_upload_mimes', 10, 1 );

  /*
     * 
     * remove wp version number from scripts and styles
     *
     */

function remove_css_js_version( $src ) {
  if( strpos( $src, '?ver=' ) )
      $src = remove_query_arg( 'ver', $src );
  return $src;
}
add_filter( 'style_loader_src', 'remove_css_js_version', 9999 );
add_filter( 'script_loader_src', 'remove_css_js_version', 9999 );


  /*
     * 
     * AJAX get posts by letter [homepage]
     *
     */


     function get_posts_by_letter() {
      $letter = $_GET['letter'];
      add_filter( 'posts_where', 'search_by_letter', 10, 2 );
      $args = array(
          'post_type' => 'post',
          'posts_per_page' => -1,
          'orderby' => 'title',
          'order' => 'ASC',
      );
      $posts = new WP_Query($args);
      remove_filter( 'posts_where', 'search_by_letter', 10, 2 );
      $output = '';
      $counter = 0; // counter for every 3 posts
      while ($posts->have_posts()) {
          $posts->the_post();
          $title = get_the_title();
          $link = get_permalink();
          $image = get_the_post_thumbnail_url(null, 'medium');
          $alt = get_the_title();
          
          // Create a new card for every 3 posts
          if ($counter % 3 == 0) {
              $output .= '<div class="card-deck">';
          }
  
          // Add a new card
          $output .= '<div class="card">';
          $output .= '<img class="card-img-top" src="' . $image . '" alt="' . $alt . '">';
          $output .= '<div class="card-body">';
          $output .= '<h4 class="card-title">' . $title . '</h4>';
          $output .= '<a href="' . $link . '" class="btn btn-card">Apri Scheda</a>';
          $output .= '</div></div>';
  
          // Close the card deck for every 3 posts
          if ($counter % 3 == 2 || $counter == $posts->post_count - 1) {
              $output .= '</div>';
          }
  
          $counter++;
      }
      wp_reset_postdata();
      if (empty($output)) {
          $output = 'No posts found';
      }
      wp_send_json_success($output);
  }
  
  function search_by_letter($where, &$query) {
      global $wpdb;
      $letter = $_GET['letter'];
      $where .= " AND $wpdb->posts.post_title LIKE '$letter%'";
      return $where;
  }
  
  add_action('wp_ajax_get_posts_by_letter', 'get_posts_by_letter');
  add_action('wp_ajax_nopriv_get_posts_by_letter', 'get_posts_by_letter');


?>