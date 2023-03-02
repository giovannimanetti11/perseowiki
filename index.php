<?php get_header(); ?>

<h1><?php bloginfo('name'); ?></h1>

<nav>
    <?php
    wp_nav_menu(array(
        'theme_location' => 'perseowiki-primary-menu',
        'container' => false,
        'items_wrap' => '<ul>%3$s</ul>'
    ));
    ?>
    <?php get_search_form(); ?>
</nav>


<?php get_footer();  ?>