<!doctype html>
<html lang="it">
<head>
 
    <!--=== META TAGS ===-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="description" content="">
    <meta name="author" content="PerSeoDesign">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
     
    <!--=== LINK TAGS ===-->
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>"  rel="stylesheet">
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS2 Feed" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
 
    <!--=== TITLE ===-->  
    <title><?php bloginfo( 'name' ); ?></title>
     
    <!--=== WP_HEAD() ===-->
    <?php wp_head(); ?>
      
</head>

<body <?php body_class(); ?>>

<?php
$custom_logo_id = get_theme_mod( 'custom_logo' );
$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
?>

<nav class="navbar navbar-light">
  <a class="navbar-brand" href="#">
    <?php if ( has_custom_logo() ) { 
        echo '<img src="' . esc_url( $logo[0] ) . '" width="30" height="30" class="logo d-inline-block align-top" alt="' . get_bloginfo( 'name' ) . '">';
        echo '<h1 class="d-inline-block">' . get_bloginfo('name') . '</h1>';
    } else {
        echo '<h1 class="d-inline-block">' . get_bloginfo('name') . '</h1>';
    }
    ?>
  </a>
  <?php
    wp_nav_menu(array(
        'theme_location' => 'perseowiki-primary-menu',
        'container' => false,
        'items_wrap' => '<ul>%3$s</ul>'
    ));
    ?>
    <button class="btn" type="button">Subscribe</button>
    <span class="navbar-text">
      Login <i class="fa-solid fa-angle-down"></i>
    </span>
</nav>

