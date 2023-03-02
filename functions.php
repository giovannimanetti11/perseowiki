<?php

define("THEME_DIR", get_template_directory_uri());

function perseowiki_support() {

  /**
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

  /**
   * Add featured images to post and pages.
   *
   * @link https://codex.wordpress.org/Post_Thumbnails
   */

  add_theme_support( 'post_thumbnails' );

}

add_action( 'after_setup_theme', 'perseowiki_support' );

function perseowiki_nav_menus() {
  
  // This theme uses wp_nav_menu() in one location.

  register_nav_menus([
    'perseowiki-primary-menu' => esc_html__( 'Primary Menu', 'perseowiki' ),
  ]);
}

add_action( 'init', 'perseowiki_nav_menus' );

function perseowiki_styles() {

  wp_enqueue_style('perseowiki-style', get_template_directory_uri().'/style.css');

}

add_action('wp_enqueue_scripts', 'perseowiki_styles')

?>