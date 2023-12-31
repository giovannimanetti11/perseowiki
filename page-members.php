<?php
/**
* Template Name: Members Page
*/

get_header();

if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    echo '<div class="about-container">';
    the_content();
    echo '</div>';
  }
}

$categories = ['Developer', 'scientific-committee'];



get_footer();
?>
