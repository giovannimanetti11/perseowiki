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

foreach($categories as $category_slug) {
  $category = get_term_by('slug', $category_slug, 'member_category');

  $args = array(
    'post_type' => 'members',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'tax_query' => array(
      array(
        'taxonomy' => 'member_category',
        'field'    => 'slug',
        'terms'    => $category_slug,
      ),
    ),
  );
  
  $query = new WP_Query($args);

  if($query->have_posts()) {
    echo '<div class="members-container">';
    echo '<h2 class="category-title">'. $category->name .'</h2>';
    echo '<div class="members-grid">';
    while($query->have_posts()) {
      $query->the_post();

      $member_name = get_the_title();
      $member_image = get_the_post_thumbnail();
      $member_affiliation = get_post_meta(get_the_ID(), '_affiliation', true);

      echo '<div class="member-card member-card-'. $category_slug .'">';
      echo '<div class="member-image">'.$member_image.'</div>';
      echo '<h3 class="member-name">'. $member_name .'</h3>';
      echo '<p class="member-affiliation">'. $member_affiliation .'</p>';
      echo '</div>';
    }
    echo '</div>';
    echo '</div>';
  }
  wp_reset_postdata();
}

get_footer();
?>
