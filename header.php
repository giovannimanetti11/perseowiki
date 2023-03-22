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
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS2 Feed" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
 
    <!--=== TITLE ===-->  
    <title><?php bloginfo( 'name' ); ?></title>
     
    <!--=== WP_HEAD() ===-->
    <?php wp_head(); ?>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-GZ4J8CZ4CW"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-GZ4J8CZ4CW');
    </script>

      
</head>

<body <?php body_class(); ?>>

<?php
$custom_logo_id = get_theme_mod( 'custom_logo' );
$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
?>

<header>

    <div class="logo">
        <a class="" href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php if ( has_custom_logo() ) { 
                echo '<img src="' . esc_url( $logo[0] ) . '" width="30" height="30" class="" alt="' . get_bloginfo( 'name' ) . '">';
                echo '<h1 class="d-inline-block">' . get_bloginfo('name') . '</h1>';
            } else {
                echo '<h1 class="d-inline-block">' . get_bloginfo('name') . '</h1>';
            }
            ?>
        </a>
    </div>
    <div class="mobileMenu">
        <i id="menu-icon" class="fa-solid fa-bars"></i>
    </div>
    <div id="popup-menu" class="mobileMenuPopup">
        <?php
            wp_nav_menu(array(
                'theme_location' => 'perseowiki-primary-menu',
                'container' => false,
                'items_wrap' => '<ul>%3$s</ul>'
            ));
        ?>
        <button class="btn btn-sm" type="button">Subscribe</button>
        <button class="btn btn-sm" type="button">Login</button>
    </div>
    <nav class="menu">
        <?php
            wp_nav_menu(array(
                'theme_location' => 'perseowiki-primary-menu',
                'container' => false,
                'items_wrap' => '<ul>%3$s</ul>'
            ));
        ?>
    </nav>
    
    <div class="call-to-action">
        <button class="btn btn-sm" type="button">Subscribe</button>
        <button class="btn btn-sm" type="button">Login</button>
    </div>

</header>




