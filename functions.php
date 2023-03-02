<?php

define("THEME_DIR", get_template_directory_uri());

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

}
add_action( 'after_setup_theme', 'perseowiki_support' );

function perseowiki_nav_menus() {
  
  // This theme uses wp_nav_menu() in one location.
  register_nav_menus([
    'really-simple-primary-menu' => esc_html__( 'Primary Menu', 'perseowiki' ),
  ]);
}
add_action( 'init', 'perseowiki_nav_menus' );

?>