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







?>