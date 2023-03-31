<!doctype html>
<html lang="it">
<head>
 
    <!--=== META TAGS ===-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="author" content="WikiHerbalist">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
     
    <!--=== LINK TAGS ===-->
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS2 Feed" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

    <title><?php wp_title(''); ?></title>

    </head>
     
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

<body <?php body_class(); ?> data-post-id="<?php echo get_the_ID(); ?>">


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
        <button id="mailingList-popup-btn-mobile" class="btn btn-sm" type="button">Mailing List</button>
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
        <button id="mailingList-popup-btn" class="btn btn-sm" type="button">Mailing List</button>
    </div>
    <!-- MAILING LIST POPUP -->
    <div id="mailingList-popup" class="popup">
        <div class="popup-content">
        <a href="#" id="mailingList-popup-close-btn"><i class="fa fa-times"></i></a>
            <h2>Iscriviti alla Mailing List di WikiHerbalist</h2>
            <form id="subscribe-form" novalidate>

                <input type="text" id="nome" placeholder="Nome" />
                <input type="text" id="cognome" placeholder="Cognome" />
                <input type="email" id="email" placeholder="Indirizzo Email" />

                <div class="alert alert-success hidden" role="alert" id="mailingList-success-message">
                    Grazie per esserti iscritto alla mailing list di WikiHerbalist
                </div>
                <div class="alert alert-danger hidden" role="alert" id="mailingList-error-message">
                    Compila i campi per completare la tua iscrizione.
                </div>

                <button type="submit" id="subscribe-btn" class="btn">Iscriviti</button>
            </form>
        </div>
    </div>
    <!-- LOGIN POPUP 
    <div id="login-popup" class="popup">
        <div class="popup-content">
            <a href="#" id="login-popup-close-btn"><i class="fa fa-times"></i></a>
            <h2>Accedi all'area personale</h2>
            <div class="no-account-create">
                Non hai un account WikiHerbalist? <a href="" id="signup-btn-from-login">Registrati</a>
            </div>
            <div class="login-form-row">
                <div class="col-md-6">
                    <form id="login-form" novalidate>
                        <input type="email" id="login-email" placeholder="Indirizzo Email" />
                        <input type="password" id="login-password" placeholder="Password" />
                        <div class="login-cta">
                            <button type="submit" id="login-btn" class="btn">Accedi</button> <a href="">Password dimenticata?</a>
                        </div>
                    </form>
                </div>
                <div class="divider">
                    <span></span> 
                    o 
                    <span></span>
                </div>
                <div class="col-md-6 social-login-buttons">
                    <button class="btn btn-block btn-social btn-google" type="button"><i class="fa-brands fa-google"></i> Accedi con Google</button>
                    <button class="btn btn-block btn-social btn-microsoft" type="button"><i class="fa-brands fa-microsoft"></i> Accedi con Microsoft</button>
                    <button class="btn btn-block btn-social btn-apple" type="button"><i class="fa-brands fa-apple"></i> Accedi con Apple</button>
                </div>
            </div>
        </div>
    </div>
    SIGNUP POPUP 
    <div id="signup-popup" class="popup">
        <div class="popup-content">
            <a href="#" id="signup-popup-close-btn"><i class="fa fa-times"></i></a>
            <h2>Accedi all'area personale</h2>
            <div class="no-account-create">
                Hai già un account WikiHerbalist? <a href="" id="login-btn-from-signup">Entra</a>
            </div>
            <div class="signup-form-row">
                <div class="col-md-6">
                    <form id="signup-form" novalidate>
                        <input type="email" id="signup-email" placeholder="Indirizzo Email" />
                        <input type="password" id="signup-password" placeholder="Password" />
                        <div class="alert alert-success hidden" role="alert" id="signup-success-message">
                        </div>
                        <div class="alert alert-danger hidden" role="alert" id="signup-error-message">
                        </div>
                        <div class="signup-cta">
                            <button type="submit" id="signup-btn" class="btn">Registrati</button>
                        </div>
                    </form>
                </div>
                <div class="divider">
                    <span></span> 
                    o 
                    <span></span>
                </div>
                <div class="col-md-6 social-login-buttons">
                    <button class="btn btn-block btn-social btn-google" type="button"><i class="fa-brands fa-google"></i> Registrati con Google</button>
                    <button class="btn btn-block btn-social btn-microsoft" type="button"><i class="fa-brands fa-microsoft"></i> Registrati con Microsoft</button>
                    <button class="btn btn-block btn-social btn-apple" type="button"><i class="fa-brands fa-apple"></i> Registrati con Apple</button>
                </div>
            </div>
        </div>
    </div> -->
</header>




